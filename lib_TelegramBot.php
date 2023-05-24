<?php

class Bot
{

    public $token;
    public array $data;
    public $chat_id;
    public $message_id;
    public $first_name;
    public $data_text;
    public $arr_data_text;
    public $request_msg;
    public $callback_query_id;
    public $calendar_date;
    public $file_id;


    function __construct($token)
    {
        $this->token = $token;
        $this->data = json_decode(file_get_contents('php://input'), true);

        file_put_contents('/home/lashuk/acces_root/login.txt', print_r($this->data, true));

        if (!empty($this->data['message']['text'])) {

            $this->chat_id = $this->data['message']['chat']['id'];
            $this->message_id = $this->data['message']['message_id'];
            $this->first_name =  $this->data['message']['from']['first_name'];
            $this->data_text = trim($this->data['message']['text']);
            $this->arr_data_text = explode(" ", $this->data_text);
            $this->request_msg = 'message_query';

        } elseif (!empty($this->data['callback_query']['data'])) {


            $this->callback_query_id = $this->data['callback_query']['id'];
            $this->chat_id = $this->data['callback_query']['from']['id'];
            $this->message_id = $this->data['callback_query']['message']['message_id'];
            $this->first_name = $this->data['callback_query']['from']['first_name'];
            $this->data_text = trim($this->data['callback_query']['data']);
            $this->arr_data_text = explode(" ", $this->data_text);
            $this->request_msg = 'callback_query';

            #Отслеживаем изменения в календаре
            if ($this->arr_data_text[0] == 'calendar') {

                if ($this->arr_data_text[1] == 'change_month') {

                    $this->editMessageText('this_chat', 'this_message', 'Календарь', calendar($this->arr_data_text[2]));

                    $this->answerCallbackQuery();
                } elseif ($this->arr_data_text[1] == 'number') {

                    $this->calendar_date = $this->arr_data_text[2];

                    $this->answerCallbackQuery();
                }
            }
        }elseif(!empty($this->data['message']['photo'])){

            $this->file_id = $this->data['message']['photo'][2]['file_id'];
            $this->chat_id = $this->data['message']['chat']['id'];
            $this->message_id = $this->data['message']['message_id'];
            $this->first_name =  $this->data['message']['from']['first_name'];
            $this->data_text = trim($this->data['message']['caption']);
            $this->arr_data_text = explode(" ", $this->data_text);
            $this->request_msg = 'photo_query';

            if($this->data_text == 'id'){
                #в caption должно быть слово id 
               $this->sendMessage("this_chat","Загруженный файл file_id:\n".$this->file_id); 
            }
            
            
        }
        #$this->sendMessage('this_chat','Ваш file_id:');
        #$this->sendMessage('this_chat','Ваш file_id:'.$this->data['message']['photo'][2]['file_id']);

    }

    /*
 use params answerCallbackQuery
    show_alert(true or false) AND 'text'
    OR
    (empty params)
 */
    public function answerCallbackQuery($show_alert = false, $text  = '')
    {

        return $this->request('answerCallbackQuery', ['callback_query_id' => $this->callback_query_id, 'parse_mode' => 'HTML', 'text' => $text, 'show_alert' => $show_alert,]);
    }

    /*
 use params sendMessage
 
    required 'this_chat OR use chat_id' AND 'text'
    optional $reply_markup
  
    example
    $bot->sendMessage('this_chat','Вы отправили обычное сообщение с клавиатурой:',$inline_kb->created_keyboard);
    $bot->sendMessage('123456','Вы отправили обычное сообщение конкретному пользователю:');
 */

    public function sendMessage($f_chat_id, $text, $reply_markup = '')
    {
        if ($reply_markup) {
            $reply_markup = json_encode($reply_markup);
        }
        if ($f_chat_id == 'this_chat') {
            $f_chat_id = $this->chat_id;
        }

        return $this->request('sendMessage', ['chat_id' => $f_chat_id, 'parse_mode' => 'HTML', 'text' => $text, 'reply_markup' => $reply_markup,]);
    }

    /*
 use params sendPhoto
 
 required 'this_chat OR use chat_id' AND 'text' AND 'file.jpg/png OR file_id' AND 'file OR file_id'
 optional $reply_markup

 example
 $file_id='AgACAgIAAxkBAAIGX2RuHAjOGrWmyiEsH0wSfST5wQ24AAKGyjEbPWlxSyP0WBzC3V_KAQADAgADeAADLwQ';
 $bot->sendPhoto('this_chat', 'Картинка',$file_id,'file_id',$inline_kb->created_keyboard); 
 $bot->sendPhoto('123456', 'Картинка для конкретного пользователя','logo.png','file');
 $bot->sendPhoto('this_chat', 'Картинка с клавиатурой','logo.png','file',$inline_kb->created_keyboard);
*/
    public function sendPhoto($f_chat_id, $text, $photo,$param, $reply_markup = '')
    {
        if ($reply_markup) {
            $reply_markup = json_encode($reply_markup);
        }
        if ($f_chat_id == 'this_chat') {
            $f_chat_id = $this->chat_id;
        }
        if($param=='file'){
             $photo = curl_file_create(__DIR__ . "/$photo",);
        }

        return $this->request('sendPhoto', ['chat_id' => $f_chat_id, 'caption' => $text, 'photo' => $photo, 'reply_markup' => $reply_markup,]);
    }

    /*
    use params editMessageMedia
 
    required 'this_chat OR use chat_id' AND 'this_message OR use message_id' AND 'text'  AND ' file_id'
    optional $reply_markup
   
    example
    $file_id = "AgACAgIAAxkBAAIGaGRuHUV32ZSGp5RoyVu3ybjWF17vAAJAyDEbPWlxS2YrB_5MS9SyAQADAgADeAADLwQ";
    $bot->editMessageMedia('this_chat','this_message','Картинка',$file_id,$inline_kb->created_keyboard);
   */   
    public function editMessageMedia($f_chat_id, $f_message_id, $text, $photo_id, $reply_markup = '')
    {
        if ($reply_markup) {
            $reply_markup = json_encode($reply_markup);
        }
        if ($f_chat_id == 'this_chat') {
            $f_chat_id = $this->chat_id;
        }
        if ($f_message_id == 'this_message') {
            $f_message_id = $this->message_id;
        }
        
        $data_photo = [
            'type' => 'photo',
            'media' =>  $photo_id,
            'caption' => $text,
            'parse_mode' => 'html',
        ];
        return $this->request('editMessageMedia', ['chat_id' => $f_chat_id, 'message_id' => $f_message_id, 'media' => json_encode($data_photo), 'reply_markup' => $reply_markup,]);
    }

    /*
 use params sendDocument
 
 required 'this_chat OR use chat_id' AND 'text' AND 'file.jpg/png'
 optional $reply_markup

 example
 $bot->sendDocument('this_chat', 'документ','logo.png');
 $bot->sendDocument('123456', 'Документ','logo.png');
 $bot->sendDocument('this_chat', 'Документ с клавиатурой','logo.png',$inline_kb->created_keyboard);
*/


    public function sendDocument($f_chat_id, $text, $file, $reply_markup = '')
    {
        if ($reply_markup) {
            $reply_markup = json_encode($reply_markup);
        }
        if ($f_chat_id == 'this_chat') {
            $f_chat_id = $this->chat_id;
        }

        return $this->request('sendDocument', ['chat_id' => $f_chat_id, 'caption' => $text, 'document' => curl_file_create(__DIR__ . "/$file",), 'reply_markup' => $reply_markup,]);
    }




    /*
 use params editMessageText
 
    required 'this_chat OR use chat_id' AND 'this_message OR use message_id' AND 'text'
    optional 'reply_markup'
  
    example
    $bot->editMessageText('this_chat','this_message', 'Измененное сообщение',$inline_kb->created_keyboard);
    OR
    $bot->editMessageText('123456','123456', 'Измененное сообщение',$inline_kb->created_keyboard);
 */
    public function editMessageText($f_chat_id, $f_message_id, $text, $reply_markup = '')
    {
        if ($reply_markup) {
            $reply_markup = json_encode($reply_markup);
        }
        if ($f_chat_id == 'this_chat') {
            $f_chat_id = $this->chat_id;
        }
        if ($f_message_id == 'this_message') {
            $f_message_id = $this->message_id;
        }

        return $this->request('editMessageText', ['chat_id' => $f_chat_id, 'message_id' => $f_message_id, 'parse_mode' => 'HTML', 'text' => $text, 'reply_markup' => $reply_markup,]);
    }

    /*
 use params editMessageText
 
    required 'this_chat OR use chat_id' AND 'this_message OR use message_id' AND 'text'
 
    example
    $bot->deleteMessage ('this_chat', 'this_message');
    OR
    $bot->deleteMessage ('123456', '123456');
 */
    function deleteMessage($f_chat_id, $f_message_id)
    {
        if ($f_chat_id == 'this_chat') {
            $f_chat_id = $this->chat_id;
        }
        if ($f_message_id == 'this_message') {
            $f_message_id = $this->message_id;
        }
        return $this->request('deleteMessage', ['chat_id' => $f_chat_id, 'message_id' => $f_message_id,]);
    }

    
    
    
    
    public function request($method, $params = '')
    {
        $ch = curl_init();
        $ch_post = [
            CURLOPT_URL => 'https://api.telegram.org/bot' . $this->token . '/' . $method,
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => $params
        ];

        curl_setopt_array($ch, $ch_post);
        $data = curl_exec($ch);
        $data = json_decode($data, true);
        if ($data['error_code']) {
            file_put_contents('/home/lashuk/acces_root/error_bot.txt', print_r($data, true),FILE_APPEND);
        }
    }
}


class keyboard
{

    /*

example #1
$inline_kb = new keyboard();

$inline_kb->create_keyboard('inline_keyboard',3,true);#'inline_keyboard OR keyboard ','number of lines','ресайз true или false'
$inline_kb->add('button 1','but1',0); #'lable text', 'callback_data', 'line number'
$inline_kb->add('button 2','but2',0);
$inline_kb->add('button 3','but3',0);

$inline_kb->add('button 4','but4',1);
$inline_kb->add('button 5','but5',1);
$inline_kb->add('button 6','but6',1);

$inline_kb->add('button 7','but7',2);

example#2
$reply_kb = new keyboard();
$reply_kb->create_keyboard('keyboard',2,true);
$reply_kb->add('button 1','',0);
$reply_kb->add('button 2','',0);
$reply_kb->add('button 3','',1);
*/
    public array $created_keyboard = [];
    public $keyboard;

    public function create_keyboard($keyboard, $count_row, $resize_keyboard = true)
    {

        $this->keyboard = $keyboard;
        $this->created_keyboard = [$keyboard => [], 'resize_keyboard' => $resize_keyboard];

        for ($i = 1; $i <= $count_row; $i++) {
            array_push($this->created_keyboard[$keyboard], []);
        }
    }



    public function add(string $lable, string $callback_data = '', int $index_row)
    {
        if ($this->keyboard == 'inline_keyboard') {
            array_push($this->created_keyboard[$this->keyboard][$index_row], ['text' => $lable, 'callback_data' => $callback_data]);
        } elseif ($this->keyboard == 'keyboard') {
            array_push($this->created_keyboard[$this->keyboard][$index_row], ['text' => $lable]);
        }
    }
}





function calendar($date = '')
{

    if ($date == '') {
        $date = date('Y-m-d');
    }


    $date_forward_month  = strtotime('+1 MONTH', strtotime($date));

    $forward_month = date('Y-m-d', $date_forward_month);

    $date_back_month  = strtotime('-1 MONTH', strtotime($date));

    $back_month = date('Y-m-d', $date_back_month);



    $arr_date = explode("-", $date);

    $months = array(1 => 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');


    $n = date("N", mktime(0, 0, 0, $arr_date[1], 01, $arr_date[0])); # 2023-05-01 проверяем на какой день недели выпадает 1 число запрошенного месяца
    $count_day = date("t", mktime(0, 0, 0, $arr_date[1], 01, $arr_date[0])); # узнаем количество дней в месяце по дате 
    $year = date("Y", mktime(0, 0, 0, $arr_date[1], 01, $arr_date[0]));
    $month = date("n", mktime(0, 0, 0, $arr_date[1], 01, $arr_date[0]));
    $month_m = date("m", mktime(0, 0, 0, $arr_date[1], 01, $arr_date[0]));

    $calendar = new keyboard();
    $calendar->create_keyboard('inline_keyboard', 8, true); #'inline_keyboard или keyboard ','количество строк','ресайз true или false'
    $calendar->add('<<<', "calendar change_month $back_month", 0); #back_month
    $calendar->add("$months[$month] $year", 'null', 0);
    $calendar->add('>>>', "calendar change_month $forward_month", 0);

    $calendar->add('Пн', 'calendar null', 1);
    $calendar->add('Вт', 'calendar null', 1);
    $calendar->add('Ср', 'calendar null', 1);
    $calendar->add('Чт', 'calendar null', 1);
    $calendar->add('Пт', 'calendar null', 1);
    $calendar->add('Сб', 'calendar null', 1);
    $calendar->add('Вс', 'calendar null', 1);



    $num_str = 2;
    $num_week = 0;
    $a = 1;
    for ($i = 1; $i <= 42; $i++) {
        $a++;

        if ($num_week == 7) {
            $num_str++;
            $num_week = 0;
        }
        $num_week++;

        if ($n == 1) {
            if ($i <= $count_day) {
                $calendar->add("$i", "calendar number $year-$month_m-$i", $num_str);
            } elseif ($i > $count_day and $i <= 35 and $count_day > 28) {
                $calendar->add(" ", 'calendar null', $num_str);
            }
        } elseif ($n > 1 and $n <= 7) {
            if ($a <= $n) {
                $calendar->add(" ", 'calendar null', $num_str);
                if ($a == $n) {
                    $i = $i - $n + 1;
                }
            } elseif ($i <= $count_day) {
                $calendar->add("$i", "calendar number $year-$month_m-$i", $num_str);
            } elseif ($i > $count_day and $i + $n - 1 <= 35 and $count_day + $n - 1 <= 35) {

                $calendar->add(" ", 'calendar null', $num_str);
            } elseif ($i > $count_day and $i + $n - 1 >= 36 and $i + $n - 1 <= 42 and $count_day + $n - 1 >= 36) {
                $calendar->add(" ", 'calendar null', $num_str);
            }
        }
    }

    return $calendar->created_keyboard;
}
