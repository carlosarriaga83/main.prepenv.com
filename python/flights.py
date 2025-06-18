



import requests
from bs4 import BeautifulSoup




import re
from typing import Dict

def extract_info(s: str) -> Dict[str, str]:
    result = {}

    price_match = re.search(r'From (\d+) Canadian dollars', s)
    if price_match:
        result['Price'] = price_match.group(1)

    stops_match = re.search(r'(\w+) flight', s)
    if stops_match:
        result['Stops'] = stops_match.group(1)

    leaving_time_match = re.search(r'Leaves .+ at ([\d:]+\s+[APM]+)', s)
    if leaving_time_match:
        result['Leaving Time'] = leaving_time_match.group(1)

    arrival_time_match = re.search(r'arrives at .+ at ([\d:]+\s+[APM]+)', s)
    if arrival_time_match:
        result['Arrival Time'] = arrival_time_match.group(1)

    duration_match = re.search(r'Total duration ([\d\s\w]+)\.', s)
    if duration_match:
        result['Duration'] = duration_match.group(1)

    airline_match = re.search(r'with (.+?)\.', s)
    if airline_match:
        result['Airline'] = airline_match.group(1)

    # remove any special character
    for key in result:
        result[key] = re.sub(r'\W+', ' ', result[key])

    return result



url = 'https://www.google.com/travel/flights/search?tfs=CBwQAhokEgoyMDI1LTA2LTA1KAAyAkY4agcIARIDWVlacgcIARIDQ1VOGiQSCjIwMjUtMDYtMTIoADICRjhqBwgBEgNDVU5yBwgBEgNZWVpAAUgBcAGCAQsI____________AZgBAQ'
response = requests.get(url)

soup = BeautifulSoup(response.text, 'html.parser')
elements = soup.find_all(class_='JMc5Xc')

for element in elements:
    data_raw = element.get('aria-label')
    data_array = extract_info(data_raw)
    print(data_array)

url = 'https://www.google.com/travel/explore?tfs=CBwQAxodagwIAhIIL20vMGg3aDZyDQgEEgkvbS8wYjkwX3IaHWoNCAQSCS9tLzBiOTBfcnIMCAISCC9tLzBoN2g2QAFIAXACggEECAQQApgBAbIBBBgBIAE&tfu=GgA'

response = requests.get(url)

soup = BeautifulSoup(response, 'html.parser')

elements = soup.find_all(class_='tsAU4e')

for element in elements:
    TO = element.find(class_='W6bZuc YMlIz').text
    DATES = element.find(class_='CQYfx').text
    AIRLINE = element.find(class_='C5fbBf P2UJoe')['aria-label']
    DURATION = element.find(class_='Xq1DAb').text
    PRICE = element.find(class_='QB2Jof xLPuCe').text
    print(f'TO: {TO}, DATES: {DATES}, AIRLINE: {AIRLINE}, DURATION: {DURATION}, PRICE: {PRICE}')
    
input()
    
    
    