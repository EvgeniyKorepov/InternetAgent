# Internet Agent
Универсальное приложение информирования, управления и оповещения.

#### Внимание - шаблон не содержит кода взаимодействия с реальным API. Вариантов Home API много и разных, использование какого то конкретного API лишь запутает пример. Вместо реальных вызовов используются программные заглушки, вы должны самостоятельно реализовать обращения к вашим устройствам. И поделится вашим решением :-)

- Android версия приложения https://play.google.com/store/apps/details?id=ru.flintnet.InternetAgent
- Windows версия приложения https://flintnet.ru/soft/InternetAgentSetup.exe

Ключ доступа к демонстраниционному API: 
- Для вставки из буфера обмена 7b2255524c223a2268747470733a2f2f666c696e746e65742e72752f6170692f617070686f6d652f222c22546f6b656e223a22333937313536373735633430616339346236336666323539363230303635227d
- Этот же ключ в текстовом виде и виде QR-кода доступен тут http://internetagent.flintnet.ru/

Ключ доступа является HEX строкой JSON строки содержащей ссылку на API и токен доступа нужного пользователя, пример в файле APIKeyGenerate.php 

Области применения:
- Умный дом - информация, управление, оповещения о событиях.
- Портал для семьи/сообщества - новости, оповещения, управление, общение.
- Простые задачи, к примеру камеры наблюдения и оповещения о движении или смене состояния.

Весь контент для приложения формируется в виде обычных html страниц. Это облегчает отладку и настройку - сначала убедитесь что все хорошо работает в браузере под требуемой платформой, тогда вы будете уверены что и в приложении все будет выглядеть аналогично.

Приложение может содержать до четырех разделов, функционально они ничем не отличаются, вы просто можете разделять информацию/функционал по своему усмотрению :
- news : к примеру новости, лента событий, пямятки и т.п.
- info : к примеру информация, перечень состояний (устройств, вещей, дверей и т.д.), http:, tel:, mailto: ссылки для удобного быстрого вызова.
- services : к примеру кнопки изменения состояния устройств, оборудования, функций и т.п. 
- support : к примеру лента сообщений, оповещений. Это единственная секция с дополнительным функционалом, при read_only=false вы можете показать диалог отправки сообщений на ваш сервер.

Описание шаблонных примеров:
- Папка web - пример точки входа на вашем web-сервере.
- Папка InternetAgentApi - пример простейшего API. 
- Файл APIKeyGenerate.php - пример генерации Токена, и Ключа доступа для приложения Интернет агент.
- InternetAgent.sql пример структуры и демо-данных базы хранения токенов пользователей и сообщений (уведомлений)
- Описание API отправки push уведомлений в проложение - в процессе...

При запуске приложение запрашивает с вашего сервера текущую конфигурацию работы в формате JSON, пример /InternetAgentApi/InternetAgentApiConfigClient.php. 
```
{
    "interface": {
        "app_title": "Мой дом", // Заголовок окна (только платформа Windows) (не обязательно)
        "toolbar_title": "Мой дом", // Текст верхней панели (не обязательно)
        "image_logo": "https:\/\/flintnet.ru\/api\/apphome\/image\/house.jpg", // Картинка/логотип верхней панели (не обязательно)
        "sections": { // Управление видимостью и заголовками разделов.
            "news": {
                "enable": false, // false - не нужно показывать, true  - показать
                "name": "Новости" // Заголовок/Наименовние раздела
            },
            "info": {
                "enable": true,
                "name": "Информация"
            },
            "services": {
                "enable": true,
                "name": "Сервисы"
            },
            "support": {
                "enable": false,
                "name": "Чат",
                "read_only": false // false - показать окно ввода и кнопку отправки сообщения
            }
        }
    },
    "application": {
        "api_url": "https:\/\/flintnet.ru\/api\/apphome\/", // Точка входя в API на вашем сервере (этот параметр передается вместе с Ключем доступа, но при смене конфигурации, аварийной смене сервера вы можете передать приложению новую точку входа, приложение запомнит его и будет обращаться к ней.
        "debug": false, // Включение отладки, актуально только для Windows
        "versions": { // Здесь вы можете управлять показом диалогов с предложением обновления приложенния (не обязательно)
            "Windows": {
                "build": 63, // Версия билда, если она больше версии билда приложения, приложение покажет диалог с предложением обновить версию.
                "update_url": "https:\/\/flintnet.ru\/soft\/InternetAgentSetup.exe" // ссылка которую приложение откроет в стандартном браузере при согласии пользователя на обновление приложения
            },
            "Android": {
                "build": 64,
                "update_url": "https:\/\/play.google.com\/store\/apps\/details?id=ru.flintnet.InternetAgent"
            },
            "iOS": {
                "build": 0,
                "update_url": ""
            },
            "macOS": {
                "build": 0,
                "update_url": ""
            }
        }
    },
    "timers": { // Настройка периодичности опроса сервера для раздела support (актуально только для Windows, для мобильных платформ вы должны посылать push оповещения)
        "message": 60, // Обычная периодичность опроса сервера в секундах
        "message_dialog": 5 // Периодичность опроса серва в секундах после отправки на сервер (эта периодичность поддерживается в течении 10 минут, потом возвращается обычная периодичность
    },
    "internet_provider_token": "03e3267fa9cc4d398cb4679ab1bc155d" // Уникальный токен вашего сервера, используется для FCM регистрации в приложении и отправке push оповещений. Вы можете сгенерировать токен самостоятельно с помошью любого GUID генератора, длина строки токена должна быть равна 32 символам.
}
```
После получение конфигурации сервера и отсуствии ошибок приложение сразу готово к работе. При открытии разделов приложение запрашивает с вашего сервера соответствующие html страницы:

Простые запросы разделов news, info и services:
- https://myserver/api/apphome/?request={"method":"news","token":"397156775c40ac94b63ff259620065"}
- https://myserver/api/apphome/?request={"method":"info","token":"397156775c40ac94b63ff259620065"}
- https://myserver/api/apphome/?request={"method":"services","token":"397156775c40ac94b63ff259620065"}

Более сложные запросы раздела support. Запросы к методу support имеют дополнительный метод (sub_method)
- https://myserver/api/apphome/?request={"method":"support","sub_method":"page","token":"397156775c40ac94b63ff259620065"} шаблон страницы. В шаблоне вы можете разместить JS скрипт LoadContent(theUrl) для подгрузки контента. Это сделано для прозрачного обновления содержимого страницы, без ее перезагрузки. 
- https://myserver/api/apphome/?request={"method":"support","sub_method":"content","token":"397156775c40ac94b63ff259620065"} контент страницы
- https://myserver/api/apphome/?request={"method":"support","sub_method":"post","token":"397156775c40ac94b63ff259620065","message":"Тело сообщения"} запрос отправки сообщения
- https://myserver/api/apphome/?request={"method":"support","sub_method":"get_last_message_id","token":"397156775c40ac94b63ff259620065"} запрос ID последнего сообщения. Приложение сравнивает полученное по запросу ID последнего сообщения сервера с ID последнего полученного сообщения, и если они отличаются, то выполняет JS скрипт LoadContent(theUrl) на загруженной странице для подгрузки контента. 
Подробнее с загрузкой страницы, обновлением ее содержимого через JS вы можете ознакомится в шаблонном примере. 


Примеры реальных запросов к API демо-сервера:
- Запрос конфигурации https://flintnet.ru/api/apphome/?request={"method":"config","token":"397156775c40ac94b63ff259620065"}
- Лента напоминаний https://flintnet.ru/api/apphome/?request={"method":"news","token":"397156775c40ac94b63ff259620065"}
- Информация https://flintnet.ru/api/apphome/?request={"method":"info","token":"397156775c40ac94b63ff259620065"}
- Сервисы https://flintnet.ru/api/apphome/?request={"method":"services","token":"397156775c40ac94b63ff259620065"}
- Сообщения/оповещения https://flintnet.ru/api/apphome/?request={"method":"support","sub_method":"page","token":"397156775c40ac94b63ff259620065"} В конце страницы стоит вызов JS функции LoadContent() - это сделано для наглядности, для работы в приложении он не нужен, приложение само вызовет эту функцию после загрузки страницы.
- https://flintnet.ru/api/apphome/?request={"method":"support","sub_method":"content","token":"397156775c40ac94b63ff259620065"} Контент страницы с сообщениями/оповещениями
