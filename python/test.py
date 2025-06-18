import win32com.client
from datetime import datetime
import requests  # For making HTTP requests
import json      # For creating JSON strings (still used for the overall request payload)
import re        # For potential future advanced sanitization

# --- API Configuration ---
API_URL = "https://sosmex.prepenv.com/API/WA/send/"
API_TO_NUMBER = "14167682436"  # The base number
# --- End API Configuration ---

def sanitize_string_content(text):
    """
    Sanitizes a string primarily by removing null bytes.
    """
    if text is None:
        return ""
    if not isinstance(text, str):
        try:
            text = str(text)
        except Exception:
            return "" # Fallback for unstringable objects

    text = text.replace('\x00', '') # Remove null bytes
    # Remove carriage returns that might mess with newline formatting, keep \n
    text = text.replace('\r', '')
    return text

def send_email_info_to_api(email_details):
    """
    Sends the extracted and sanitized email information to the specified API.
    The TXT field will be a multi-line string.
    """
    try:
        # Format email_details into a multi-line string for the TXT field
        # Adjust the formatting here as per the exact requirements of the API for the TXT field
        txt_payload_string = (
            f"Subject: {email_details.get('Subject', 'N/A')} "
            f"From: {email_details.get('From', 'N/A')} "
            f"Received: {email_details.get('Received', 'N/A')} "
            #f"--- Body Snippet ---\n{email_details.get('BodySnippet', '')}"
        )

        payload = {
            "TO": f"{API_TO_NUMBER}",
            "TXT": txt_payload_string  # Now a multi-line string
        }
        print(f"  Attempting to send to API: {API_URL}")
        # For debugging, print the TXT field content clearly
        print(f"  Content of TXT field being sent:\n-------\n{txt_payload_string}\n-------")
        print(f"  Full Payload (for requests library): {json.dumps(payload, indent=2)}")


        response = requests.post(API_URL, json=payload, timeout=15) # requests library handles json=payload
        response.raise_for_status()

        print(f"  SUCCESS: API call successful. Status: {response.status_code}")
        try:
            print(f"  API Response: {response.json()}")
        except json.JSONDecodeError:
            print(f"  API Response (not JSON): {response.text[:2000]}...")

    except requests.exceptions.HTTPError as http_err:
        print(f"  ERROR: HTTP error occurred during API call: {http_err}")
        if hasattr(http_err, 'response') and http_err.response is not None:
            print(f"  Response Content: {http_err.response.content}")
    except requests.exceptions.ConnectionError as conn_err:
        print(f"  ERROR: Connection error occurred during API call: {conn_err}")
    except requests.exceptions.Timeout as timeout_err:
        print(f"  ERROR: Timeout error occurred during API call: {timeout_err}")
    except requests.exceptions.RequestException as req_err:
        print(f"  ERROR: An unexpected error occurred during API call: {req_err}")
    except Exception as e:
        print(f"  ERROR: An unexpected error occurred while preparing or sending API request: {e}")


def get_most_recent_unread_emails(email_account_name, count=10):
    print(f"--- Starting process to retrieve {count} most recent unread emails for account: {email_account_name} ---")

    try:
        print("Dispatching Outlook.Application...")
        outlook = win32com.client.Dispatch("Outlook.Application")
        namespace = outlook.GetNamespace("MAPI")

        print(f"Attempting to find account folder: '{email_account_name}'...")
        found_account_folder = None
        for folder in namespace.Folders:
            if folder.Name.lower() == email_account_name.lower():
                found_account_folder = folder
                print(f"SUCCESS: Found matching account folder: '{found_account_folder.Name}'")
                break

        if not found_account_folder:
            folder_names = [f.Name for f in namespace.Folders]
            print(f"ERROR: Account folder '{email_account_name}' not found.")
            print(f"Available top-level account folders are: {folder_names}")
            return

        inbox = None
        try:
            target_inbox_name = "Inbox"
            print(f"Attempting to access folder named '{target_inbox_name}' under '{found_account_folder.Name}'...")
            inbox = found_account_folder.Folders[target_inbox_name]
            print(f"SUCCESS: Accessed inbox: '{inbox.Name}', Total items: {inbox.Items.Count}")
        except Exception:
            print(f"Could not access '{target_inbox_name}' by name. Trying GetDefaultFolder(6)...")
            try:
                inbox = found_account_folder.GetDefaultFolder(6) # olFolderInbox = 6
                print(f"SUCCESS (using GetDefaultFolder(6)): Accessed inbox: '{inbox.Name}', Total items: {inbox.Items.Count}")
            except Exception as e_gdf:
                print(f"ERROR: Could not access Inbox using GetDefaultFolder(6): {e_gdf}")
                return

        if not inbox:
            print("ERROR: Inbox could not be accessed.")
            return

        if inbox.Items.Count == 0:
            print(f"The inbox '{inbox.Name}' is empty.")
            return

        unread_filter_str = "[Unread]=True"
        print(f"Filtering for unread emails using: {unread_filter_str}")

        messages = inbox.Items
        unread_messages = messages.Restrict(unread_filter_str)

        if unread_messages.Count == 0:
            print("No unread emails found in the inbox.")
            return

        print(f"Found {unread_messages.Count} unread email(s). Sorting by ReceivedTime (descending)...")
        unread_messages.Sort("[ReceivedTime]", True)

        print(f"\n--- Processing and sending up to {count} most recent unread emails to API ---")

        processed_count = 0
        for i in range(min(count, unread_messages.Count)):
            try:
                message_item = unread_messages.Item(i + 1)
                if message_item.Class == 43: # olMail
                    processed_count += 1
                    print(f"\n--- Processing Unread Email {processed_count} for API ---")

                    sanitized_subject = sanitize_string_content(message_item.Subject)
                    sanitized_sender_name = sanitize_string_content(message_item.SenderName)
                    sanitized_sender_email = sanitize_string_content(message_item.SenderEmailAddress)

                    print(f"  Subject: {sanitized_subject}")
                    from_field_text = f"{sanitized_sender_name} ({sanitized_sender_email})" if sanitized_sender_name and sanitized_sender_email else sanitized_sender_name or sanitized_sender_email or "N/A"
                    print(f"  From: {from_field_text}")


                    received_dt_obj = message_item.ReceivedTime
                    try:
                        received_time_str = received_dt_obj.strftime('%Y-%m-%d %H:%M:%S %Z%z')
                    except ValueError:
                        received_time_str = received_dt_obj.strftime('%Y-%m-%d %H:%M:%S')
                    print(f"  Received Time: {received_time_str}")

                    body_content = message_item.Body
                    body_snippet = (body_content[:250] + "...") if len(body_content) > 250 else body_content
                    sanitized_body_snippet = sanitize_string_content(body_snippet.strip()) # Strip first, then sanitize

                    email_info_for_api = {
                        "Subject": sanitized_subject,
                        "From": from_field_text,
                        "Received": received_time_str,
                        "BodySnippet": sanitized_body_snippet
                    }

                    send_email_info_to_api(email_info_for_api)

                    # OPTIONAL: Mark the email as read after processing
                    # message_item.UnRead = False
                    # message_item.Save()
                    # print("  Marked email as read.")

                else:
                    print(f"  Skipping item of class {message_item.Class} (not a MailItem). Subject: {getattr(message_item, 'Subject', 'N/A')}")

            except AttributeError as ae:
                print(f"  Attribute error processing an unread email: {ae}. Item might not be a standard mail item or property is missing.")
            except Exception as e_detail:
                print(f"  Error reading details or sending one unread email: {e_detail}")

            if processed_count >= count:
                break

        if processed_count == 0:
             print(f"No mail items found among the first {min(count, unread_messages.Count)} unread items, or errors occurred processing them.")
        print("-" * 50)

    except Exception as e:
        print(f"\n--- An OVERALL error occurred ---")
        import traceback
        traceback.print_exc()

if __name__ == "__main__":
    account_to_check = "Carlos.Arriaga@metrolinx.com"
    number_of_emails_to_process = 5 # Test with 1 first
    
    get_most_recent_unread_emails(account_to_check, number_of_emails_to_process)
    input("\nPress Enter to exit...")