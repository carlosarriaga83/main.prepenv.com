

import os
from wyze_sdk import Client


try:
        
    response = Client().login(
        email=os.environ['cear83@gmail.com'],
        password=os.environ['Pellu8aa1!'],
        key_id=os.environ['02b63366-c927-4c33-b6c7-8d5be20bfb81'],
        api_key=os.environ['Mhl8XsqWlxm48pSx1aTzgzBAKXBR5kaghazUrpALRg6nbcldhn3tRpFmndl0']
    )
    input('fin')
    client = Client(email="cear83@gmail.com", password="Pellu8aa1!")
    user = client.user

    devices = user.devices()
    cameras = [device for device in devices if device.product_type == "Camera"]
    input()
    for camera in cameras:
        print(camera.nickname)
        
        input()        
except Exception as e:
    print(f"An error occurred: {e}")
    input()
    
    

import os
from wyze_sdk import Client

try:
    #client = Client(email=os.environ['cear83@gmail.com'], password=os.environ['Pellu8aa1!'])
    client = Client(email="cear83@gmail.com", password="Pellu8aa1!")
    devices = client.devices.list()

    for device in devices:
        if device.product.model_name.startswith("WYZ_CAM"):
            print(f"Name: {device.nickname}, Model: {device.product.model_name}")
    input()

except Exception as e:
    print(f"An error occurred: {e}")
    input()