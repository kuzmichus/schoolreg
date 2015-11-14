<?php

Class epochtasms
   {
    /**
    * GetCommandStatus - декодирует ответ от сервера
    *
    * @param $status string - Статус комманды от сервера
    *
    * @return string Расшифровка - статус комманды от сервера
    */
    function GetCommandStatus($status)
     {
      switch($status)
       {
        case '0':
          return 'Запрос верный';
        break;

        case '-1':
          return 'Неправильный логин и/или пароль';
        break;

        case '-2':
          return 'Неправильный формат XML';
        break;

        case '-3':
          return 'Недостаточно кредитов на аккаунте пользователя';
        break;

        case '-4':
          return 'Нет верных номеров получателей';
        break;

        default:
          return 'Ответ не распознан';
        break;
      } // switch($status)

    } // GetCommandStatus


    /**
    * SendToServer - отправка запроса на сервер через cURL
    *
    * @param $xml_data string XML-запрос к серверу (SOAP)
    * @param $headers string Заголовки запроса к серверу (SOAP)
    *
    * @return string XML-ответ от сервера (SOAP)
    */
    function SendToServer($xml_data,$headers)
       {
        $ch = curl_init(); // Инициализировать библиотеку cURL
        curl_setopt($ch, CURLOPT_URL,"http://atompark.com/members/sms/xml.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Должен быть ответ (ожидание ответа) от сервера
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Задать тайм-аут работы с сокетами
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Задать заголовки HTTP запроса
        curl_setopt($ch, CURLOPT_POST, 1); // Будет POST запрос
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data); // Задать тело POST
        $data = curl_exec($ch); // Выполнить HTTP обмен
        if(curl_errno($ch))
           {
            die("Error: ".curl_error($ch));
            }
        else
           {
            curl_close($ch);
            return $data;
            }

        } // SendToServer

    /**
    * GetCreditBalance – запрос на получение баланса пользователя
    *
    * @param $login string Логин пользователя
    * @param $password string Пароль пользователя
    *
    * @return array("Ответ сервера" => (string), "Балланс" => (decimal)) Ответ сервера в виде массива данных
    */
    function GetCreditBalance($login,$password)
       {

$xml_data = '<SMS>
<operations>
<operation>BALANCE</operation>
</operations>
<authentification>
<username>'.$login.'</username>
<password>'.$password.'</password>
</authentification>
</SMS>';

        $headers = array(
            "POST /members/sms/xml.php HTTP/1.1",
            "HOST atompark.com",
            "Content-Type: text/xml; charset=utf-8",
            "Content-length: ".strlen($xml_data),
            );

        $data = $this->SendToServer($xml_data,$headers);
        // Show me the result
        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
        return array(
            "Ответ сервера" => $this->GetCommandStatus($results[1]['value']),
            "Балланс" => $results[3]['value']
            );
        } // GetCreditBalance

    /**
    * SendTextMessage - передача простого текстового SMS-сообщения
    *
    * @param $login string Логин пользователя
    * @param $password string Пароль пользователя
    * @param $destinationAddress string Мобильный телефонный номер получателя сообщения, в международном формате: код страны + код сети + номер телефона. Пример: 7903123456
    * @param $messageData string Текст сообщения, поддерживаемые кодировки IA5 и UCS2
    * @param $sourceAddress string Адрес отправителя сообщения. До 11 латинских символов или до 15 цифровых
    * @param $deliveryReport boolean Запрашивать отчет о статусе данного сообщения
    * @param $flashMessage boolean Отправка Flash-SMS
    * @param $validityPeriod integer Время жизни сообщения, устанавливается в минутах
    *
    * @return array("Ответ сервера" => (string), "ID сообщения" => (decimal)) Ответ сервера в виде массива данных
    */
    function SendTextMessage($login,$password,$destinationAddress,$messageData,$sourceAddress)
       {
$messageId = uniqid('sms');
$xml_data = '
<SMS>
<operations>
<operation>SEND</operation>
</operations>
<authentification>
<username>'.$login.'</username>
<password>'.$password.'</password>
</authentification>
<message>
<sender>'.$sourceAddress.'</sender>
<text>'.$messageData.'</text>
</message>
<numbers>
<number messageID="'.$messageId.'">'.$destinationAddress.'</number>
</numbers>
</SMS>
';

        $headers = array(
            "POST /members/sms/xml.php HTTP/1.1",
            "HOST atompark.com",
            "Content-Type: text/xml; charset=utf-8",
            "Content-length: ".strlen($xml_data),
            );

        $data = $this->SendToServer($xml_data,$headers);
        // Show me the result
        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);

    }
  }


?>