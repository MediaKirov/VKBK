# VKBK
Инструмент для создания и синхронизации локального бэкапа вашего лампового профиля ВКонтакте.

### Актуальная версия 0.8.5

### Системные требования
Софт | Версия
--- | ---
Apache | 2.4+
PHP | 5.6+
MySQL | 5.5+

## История версий
Посмотреть [историю версий](CHANGELOG.md) проекта VKBK.

### Функционал
На данный момент реализован следующий функционал:
+ __новое__ Получение диалогов (личные сообщения) (тип: фото, видео, ссылки, документы, стикеры)
- Получение альбомов (в том числе и системных)
- Получение фотографий
- Получение документов
- Получение аудиозаписей ~~и альбомов~~
> В версии 0.8.5 появилась возможность сохранять свои аудиозаписи.
>
> ВК отключил данный API, с версии 0.5.6 синхронизация аудио не будет работать
- Получение видеозаписей
> (просмотр через плеер, возможность создать локальную копию при помощи youtube-dl)
* Получение сообщений и репостов со стены (тип: фото, видео, ссылки, ~~музыка~~)

Через веб-интерфейс возможен просмотр альбомов, фотографий, стены, прослушивание аудиозаписей, просмотр видеозаписей через сторонние плееры.

### Инсталляция
[Инстукция по установке и настройке скрипта](https://github.com/Chiaki/VKBK/wiki/Установка-и-настройка)
