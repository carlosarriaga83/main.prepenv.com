


import jwt
from cryptography.hazmat.primitives import serialization
import time
import secrets




key_name       = "organizations/2394a568-a708-40bc-a595-4b47e6206dda/apiKeys/934ec8cf-e97f-4249-985a-7b9dfc645431"
key_secret     = "-----BEGIN EC PRIVATE KEY-----\nMHcCAQEEIOmuOzg/AANPY7JfXfLUSjkk75elZSw5Y5lLF8brN61koAoGCCqGSM49\nAwEHoUQDQgAE3bNufi8Wox/y1gE4Jaijfo3nj/QqpGs2MEDlpGjWOlNsD8nNJ7yE\nj14zJWpguUjOctmfcl6sd7JQrptKvvIH2A==\n-----END EC PRIVATE KEY-----\n"
request_method = "GET"
request_host   = "api.coinbase.com"
request_path   = "/api/v3/brokerage/accounts"

def build_jwt(uri):
    private_key_bytes = key_secret.encode('utf-8')
    private_key = serialization.load_pem_private_key(private_key_bytes, password=None)
    jwt_payload = {
        'sub': key_name,
        'iss': "cdp",
        'nbf': int(time.time()),
        'exp': int(time.time()) + 120,
        'uri': uri,
    }
    jwt_token = jwt.encode(
        jwt_payload,
        private_key,
        algorithm='ES256',
        headers={'kid': key_name, 'nonce': secrets.token_hex()},
    )
    return jwt_token
    
def main():
    uri = f"{request_method} {request_host}{request_path}"
    jwt_token = build_jwt(uri)
    print(jwt_token)
    input()
    
    
    import http.client
    import json
    conn = http.client.HTTPSConnection("api.coinbase.com")
    payload = ''
    headers = {
      'Content-Type': 'application/json'
    }
    conn.request("GET", "/api/v3/brokerage/accounts/8bfc20d7-f7c6-4422-bf07-8243ca4169fe", payload, headers)
    res = conn.getresponse()
    data = res.read()
    print(data.decode("utf-8"))
        
    input()

    
if __name__ == "__main__":
    main()




    
    
    

import os
from coinbase.wallet.client import Client
import requests

# Replace these with your actual Coinbase API keys
API_SECRET = '-----BEGIN EC PRIVATE KEY-----\nMHcCAQEEIOmuOzg/AANPY7JfXfLUSjkk75elZSw5Y5lLF8brN61koAoGCCqGSM49\nAwEHoUQDQgAE3bNufi8Wox/y1gE4Jaijfo3nj/QqpGs2MEDlpGjWOlNsD8nNJ7yE\nj14zJWpguUjOctmfcl6sd7JQrptKvvIH2A==\n-----END EC PRIVATE KEY-----\n'
API_KEY = 'organizations/2394a568-a708-40bc-a595-4b47e6206dda/apiKeys/934ec8cf-e97f-4249-985a-7b9dfc645431'

# Initialize the Coinbase client
client = Client(API_KEY, API_SECRET)

def get_bitcoin_price():
    try:
        print("Starting to fetch Bitcoin price...")
        
        # Fetch Bitcoin price from the Coinbase API
        response = requests.get("https://api.coinbase.com/v2/prices/spot?currency=USD")
        response.raise_for_status()  # Raise an error for bad responses
        
        print("Bitcoin price data retrieved successfully.")
        
        # Extract the price from the JSON response
        price = float(response.json()['data']['amount'])
        print("Current Bitcoin price extracted successfully.")
        
        return price

    except requests.exceptions.RequestException as e:
        print(f"An error occurred while fetching the Bitcoin price: {e}")
        return None

def get_wallet_value():
    try:
        print("Retrieving wallet value...")
        # Get account balance for Bitcoin
        accounts = client.get_accounts()
        for account in accounts['data']:
            if account['currency'] == 'BTC':
                balance = float(account['balance']['amount'])
                wallet_value = balance * get_bitcoin_price()
                print(f"Current wallet balance: {balance} BTC")
                print(f"Total wallet value in USD: ${wallet_value:.2f}")
                return wallet_value
        print("No Bitcoin wallet found.")
        return 0.0
    except Exception as e:
        print(f"An error occurred while retrieving wallet value: {e}")
        return None

def buy_bitcoin(amount):
    try:
        print(f"Attempting to buy {amount} BTC...")
        # Create a market order to buy Bitcoin
        order = client.buy(amount=amount, currency='BTC')
        print(f"Successfully bought {amount} BTC. Order details: {order}")
    except Exception as e:
        print(f"An error occurred while trying to buy Bitcoin: {e}")

def sell_bitcoin(amount):
    try:
        print(f"Attempting to sell {amount} BTC...")
        # Create a market order to sell Bitcoin
        order = client.sell(amount=amount, currency='BTC')
        print(f"Successfully sold {amount} BTC. Order details: {order}")
    except Exception as e:
        print(f"An error occurred while trying to sell Bitcoin: {e}")

if __name__ == "__main__":
    print("Fetching the current price of Bitcoin (BTC)...")
    price = get_bitcoin_price()
    
    
    

    
    
    
    if price is not None:
        print(f"The current price of Bitcoin (BTC) is: ${price:.2f}")
        
        # Retrieve and print wallet value
        wallet_value = get_wallet_value()
        
        # Example of buying and selling Bitcoin
        buy_amount = 0.001  # Example amount to buy
        sell_amount = 0.001  # Example amount to sell
        
        #buy_bitcoin(buy_amount)
        #sell_bitcoin(sell_amount)
    else:
        print("Failed to retrieve the Bitcoin price.")
        
    input()