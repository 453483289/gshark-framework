#/usr/bin/env   python

import sys
from colorama import Fore,Back,Style
import requests
import json

API_KEY = ""
API_WEBOOK = ""
API_CERTIFICATE = ""

def setWebook(url_webook, api_key, api_certificate):
    url_telegram = 'https://api.telegram.org/bot'+api_key.strip()+'/setWebhook'
    req = requests.post(url_telegram, data={'url':url_webook.strip(), 'certificate':api_certificate.strip()})
    content = req.text
    content = json.loads(content)
    if content['ok'] == True and content['description'] == "Webhook was set":
        print Fore.GREEN + "Webook setup sucessfully use /start on bot for check it" + Style.RESET_ALL

def main():
    print "Welcome to GShark Framework"
    print Fore.YELLOW + "Helper: Configure Master server" + Style.RESET_ALL
    action = False
    while(action == False):
        user_input = raw_input('Telegram bot API key $> ')
        if user_input != "" and ":" in user_input:
            API_KEY = user_input
            action = True
    action = False
    while(action == False):
        print Fore.RED + "Please use HTTPS url" + Style.RESET_ALL
        print "Please enter webook url Like (http://exemple.com/?p=webook)"
        user_input = raw_input('$> ')
        if "https://" in user_input:
            API_WEBOOK = str(user_input)
            action = True
    action = False
    while(action == False):
        print "Please enter SSL certificate path e.g: (/location/of/cert/certificate.crt)"
        user_input = raw_input('$> ')
        if user_input != "":
            API_CERTIFICATE = user_input
            action = True
    if API_WEBOOK != "" and API_KEY != "" and API_CERTIFICATE != "":
        setWebook(API_WEBOOK, API_KEY, API_CERTIFICATE)

main()