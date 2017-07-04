# First run !

### Configure Telegram account

```sh
Navigate on this url and add the botFather contact : https://telegram.me/BotFather
```

```sh
Send a message /newbot to @botFather contact and send your botName
```

```sh
Send a messsage /mybots to @botFather and select your botname
Now we can generate Token, click on API_TOKEN and select your token (LIKE : 41****:AAG5wqLsxe****nb9i)
```

telegram account as been configured !

### Database

Create a database and upload a SQL/database.sql file

### Configure Gshark master server environement

Now we need configure GShark.

Upload all gshark-framework repository to your hosting and use a folder Core/Interfaces/ for public repository


##### Open file /Core/Config/config.json

Add your bot API_KEY to telegram struct E.g :

```json
  "telegram": {
    "bot_id": "41****:AAG5wqLsxe****nb9i"
  }
```

Update database information E.g :

```json
  "database": {
    "hostname": "localhost",
    "dbname"  : "gshark",
    "username": "root",
    "password": "root"
  }
```

Update structure of path E.g: 

```json
  "structure": {
    "core_folder":"/home/GSHARK/Core/",
    "output_folder":"/home/GSHARK/Output/"
  }
```

### Configure GShark Telegram webook

Start configuration helpers like this

```sh
python Helpers/configureBot.py
```
Enter your API_TOKEN like this:

```sh
Welcome to GShark Framework
Helper: Configure Master server
Telegram bot API key $> 41****:AAG5wqLsxe****nb9i
```

Now we need configure webook url for comunicate to Telegram bot
webook url of gshark is :

your_domain.com/?p=webook

You can change it on routing framework step in GShark class

```sh
Please use HTTPS url
Please enter webook url Like (http://exemple.com/?p=webook)
$> your_domain.com/?p=webook
```

Now enter a path in master server for your SSL certificate

```sh
Please enter SSL certificate path e.g: (/location/of/cert/certificate.crt)
$> /home/gshark/certificate.crt
```
Press <key> and it's gone !
