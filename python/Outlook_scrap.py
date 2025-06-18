import imaplib
import email
from email.header import decode_header
import getpass # For securely getting password

def decode_mail_header(header_string):
    """Decodes email header, handling different charsets."""
    if not header_string:
        return ""
    decoded_parts = []
    for part, charset in decode_header(header_string):
        if isinstance(part, bytes):
            try:
                decoded_parts.append(part.decode(charset or 'utf-8', errors='replace'))
            except LookupError: # Unknown encoding
                decoded_parts.append(part.decode('utf-8', errors='replace')) # Fallback
        else:
            decoded_parts.append(part)
    return "".join(decoded_parts)

def get_emails_via_imap(imap_server, email_address, password, mailbox="INBOX", num_emails=10):
    """
    Connects to an IMAP server, retrieves emails, and prints their subject and sender.
    """
    try:
        # Connect to the server
        mail = imaplib.IMAP4_SSL(imap_server)
        
        # Login
        mail.login(email_address, password)
        
        # Select the mailbox you want to check (e.g., "INBOX")
        mail.select(mailbox)
        
        print(f"Successfully connected to {imap_server} and selected mailbox '{mailbox}'.\n")
        
        # Search for all emails in the mailbox
        status, messages_ids_bytes = mail.search(None, "ALL")
        if status != "OK":
            print("Failed to search for emails.")
            return

        email_ids = messages_ids_bytes[0].split()
        
        if not email_ids:
            print(f"No emails found in '{mailbox}'.")
            return

        print(f"Found {len(email_ids)} emails. Fetching the latest {num_emails}...\n")

        # Fetch the most recent emails (IDs are typically in ascending order)
        # To get the latest, we take the last 'num_emails' IDs
        for i in range(len(email_ids) -1, max(-1, len(email_ids) -1 - num_emails), -1):
            email_id = email_ids[i]
            status, msg_data = mail.fetch(email_id, "(RFC822)")
            
            if status == "OK":
                for response_part in msg_data:
                    if isinstance(response_part, tuple):
                        # Parse the email content
                        msg = email.message_from_bytes(response_part[1])
                        
                        subject = decode_mail_header(msg["subject"])
                        sender = decode_mail_header(msg["from"])
                        
                        print(f"Subject: {subject}")
                        print(f"Sender: {sender}")
                        print("-" * 30)
            else:
                print(f"Failed to fetch email ID {email_id.decode()}")

        # Close the connection and logout
        mail.close()
        mail.logout()
        print("\nDisconnected from the server.")

    except imaplib.IMAP4.error as e:
        print(f"IMAP Error: {e}")
        print("Please check your IMAP server details, credentials, and ensure IMAP is enabled for your account.")
    except Exception as e:
        print(f"An unexpected error occurred: {e}")

if __name__ == "__main__":
    # --- Configuration ---
    # You'll need to find these for your email provider
    # Examples:
    # Gmail: imap_server = "imap.gmail.com"
    # Outlook.com/Office365: imap_server = "outlook.office365.com"
    # Yahoo: imap_server = "imap.mail.yahoo.com"
    
    imap_host = input("Enter your IMAP server address (e.g., outlook.office365.com): ")
    user_email = input("Enter your email address: ")
    # Using getpass for password to avoid showing it on screen
    user_password = getpass.getpass("Enter your email password or app password: ") 
    
    # Optional: specify a different mailbox or number of emails
    # mailbox_to_check = "INBOX" 
    # emails_to_fetch = 5 

    get_emails_via_imap(imap_host, user_email, user_password)
    # To fetch from a specific folder or a different number of emails:
    # get_emails_via_imap(imap_host, user_email, user_password, mailbox="Sent", num_emails=5)

