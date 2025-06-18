import yfinance as yf

def get_tesla_stock_price():
    try:
        print("Starting to fetch Tesla stock data...")
        
        # Get the Tesla stock data
        tesla = yf.Ticker("TSLA")
        print("Tesla stock data retrieved successfully.")
        
        # Get the current stock price
        stock_data = tesla.history(period="1d")
        print("Extracting the latest stock price...")
        
        current_price = stock_data['Close'].iloc[0]
        print("Current stock price extracted successfully.")
        
        return current_price

    except Exception as e:
        print(f"An error occurred: {e}")
        return None

if __name__ == "__main__":
    print("Fetching the current price of Tesla Inc. stock (TSLA)...")
    price = get_tesla_stock_price()
    
    if price is not None:
        print(f"The current price of Tesla Inc. stock (TSLA) is: ${price:.2f}")
    else:
        print("Failed to retrieve the stock price.")
        
    input()