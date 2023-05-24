# API telegram PHP


## lib_TelegramBot -Easy to use php telegram library!
## What is in the library:
- Basic Queries
- Calendar
- Keyboard



## Installation
```PHP
require_once 'lib_TelegramBot.php';
```
## Using Variables
```php
    $bot->data; #The entire array received from telegrams
    $bot->chat_id;
    $bot->message_id;
    $bot->callback_query_id;
    $bot->first_name;
    $bot->request_msg;
    $bot->calendar_date;
    $bot->file_id;
    $bot->data_text; #simple message, including commands
    $bot->arr_data_text;#Array of words in the message, space separated
    #example
    $callback='but1 param 1'
    $bot->arr_data_text[0]=but1
    $bot->arr_data_text[1]=param
    $bot->arr_data_text[2]=1
    
    
    

```

## Usage bot

```PHP
$bot = new Bot('TOKEN');

if($bot->request_msg=='message_query' and $bot->data_text == '/start'){
#Intercepting a normal message
}elseif($bot->request_msg=='callback_query' and $bot->data_text == 'callback_data'){
#Intercepting the callback message
}elseif($bot->request_msg=='photo_query'){
#Intercepting the photo message
}
```
## Usage request

```php
/*
Use params answerCallbackQuery
    show_alert(true or false) AND 'text'
    OR
    (empty params)
*/
#example
    $bot->answerCallbackQuery('false','text');
    $bot->answerCallbackQuery();
    
/*
use params sendMessage
    required 'this_chat OR use chat_id' AND 'text'
    optional $reply_markup
  */ 
#example
    $bot->sendMessage('this_chat','Вы отправили обычное сообщение с клавиатурой:',$inline_kb->created_keyboard);
    $bot->sendMessage('123456','Вы отправили обычное сообщение конкретному пользователю:');

/*
use params sendPhoto
    required 'this_chat OR use chat_id' AND 'text' AND 'file.jpg/png OR file_id' AND 'file OR file_id'
    optional $reply_markup
*/
#example
    $file_id='AgACAgIAAxkBAAIGX2RuHAjOGrWmyiEsH0wSfST5wQ24AAKGyjEbPWlxSyP0WBzC3V_KAQADAgADeAADLwQ';
    $bot->sendPhoto('this_chat', 'Картинка с клавиатурой',$file_id,'file_id',$inline_kb->created_keyboard); 
    $bot->sendPhoto('123456', 'Картинка для конкретного пользователя','logo.png','file');
    $bot->sendPhoto('this_chat', 'Картинка с клавиатурой','logo.png','file',$inline_kb->created_keyboard);

/*
use params editMessageMedia
    required 'this_chat OR use chat_id' AND 'this_message OR use message_id' AND 'text'  AND ' file_id'
    optional $reply_markup
*/   
#example
    $file_id = "AgACAgIAAxkBAAIGaGRuHUV32ZSGp5RoyVu3ybjWF17vAAJAyDEbPWlxS2YrB_5MS9SyAQADAgADeAADLwQ";
    $bot->editMessageMedia('this_chat','this_message','Картинка',$file_id,$inline_kb->created_keyboard);
    $bot->editMessageMedia('123456','1234','Картинка',$file_id,$inline_kb->created_keyboard);

/*
use params sendDocument
required 'this_chat OR use chat_id' AND 'text' AND 'file.jpg/png/txt OR file_id' AND 'file OR file_id'
    optional $reply_markup
*/
 #example
    $bot->sendDocument('this_chat', 'Документ','file.txt','file');
    $bot->sendDocument('123456', 'Документ','file.txt','file');
    $bot->sendDocument('this_chat', 'Документ','$file_id','file_id');
    
/*
use params editMessageText
    required 'this_chat OR use chat_id' AND 'this_message OR use message_id' AND 'text'
    optional 'reply_markup'
*/
#example
    $bot->editMessageText('this_chat','this_message', 'Измененное сообщение',$inline_kb->created_keyboard);
    $bot->editMessageText('123456','1234', 'Измененное сообщение',$inline_kb->created_keyboard);
    
/*
use params editMessageText
    required 'this_chat OR use chat_id' AND 'this_message OR use message_id' AND 'text'
*/
#example
    $bot->deleteMessage ('this_chat', 'this_message');
    $bot->deleteMessage ('123456', '1234');
}
```

## Usage keyboard
```php
require_once 'lib_TelegramBot.php';

$bot = new Bot('TOKEN');

$inline_kb = new keyboard();
#create_keyboard('inline_keyboard OR keyboard ','number of lines','resize_keyboard  true OR false')
$inline_kb->create_keyboard('inline_keyboard',3,true)
#add('Text button','callback_data','line number')
#line 1
$inline_kb->add('Button1',"but1",0);
$inline_kb->add("Button2",'but2',0);
$inline_kb->add('BUtton3',"but3",0);
#line 2
$inline_kb->add('Button4',"but4",1);
$inline_kb->add("Button5",'but5',1);
#line 3
$inline_kb->add("Button6",'but6',2);

if($bot->request_msg=='message_query' and $bot->data_text == '/start'){
$bot->sendMessage('this_chat','Клавиатура',$inline_kb->created_keyboard);    
}
```

## Usage calendar
```php
require_once 'lib_TelegramBot.php';

$bot = new Bot('TOKEN');

if($bot->request_msg=='message_query' and $bot->data_text == '/start'){
    $bot->sendMessage('this_chat','Календарь',calendar());    
}elseif($bot->request_msg=='callback_query' and $bot->arr_data_text[0] == 'calendar'){
    $bot->sendMessage('this_chat','Вы выбрали дату:'.$bot->calendar_date);
    }
```
## Developer
Lashuk Vladimir
email: dec689@gmail.com

