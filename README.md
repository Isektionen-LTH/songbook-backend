The backend for the songbook page

# Develop
1. Build the docker image with `docker build . -t songbook`
2. Make sure the `media` folder is writable by all if you want to upload media
3. Run the server with `./startdev.sh`

# Deploy
1. Run `composer install` in the root directory to install all dependencies
2. Upload the `src` directory to your web server
3. Make sure that the `api/media` directory is writable for your webserver
4. Create the database and user, use the content in `schema.sql` for the database and create a user manually
5. Copy `config.example.php` to `config.php` in api root and configure the values with correct credentials. (Change the admin password!)
6. ...
7. Profit!

# Documentation

Since this is a small project with few endpoints, the documentation will be contained in this README.

## Routes

| Route               | Supported methods         | Description                                     |
|---------------------|---------------------------|-------------------------------------------------|
| /api/songs          | GET                       | Get a list of all songs (general information)   |
| /api/song/:uuid     | GET, POST, (PUT), DELETE  | CRUD for a specific song, detailed information  |
| /api/categories     | GET                       | Get a list of all categories                    |
| /api/category/:uuid | GET, POST, (PUT), DELETE  | CRUD for a specific category                    |
| /api/media/:hash    | GET, POST, (PUT), DELETE  | CRD for a media object, update is not supported |
| /api/changepassword | POST                      | Change the admin password                       |

### Detailed routes

- All routes can return 5xx codes other than specified if something is wrong with the server of course
- All `GET` requests are open for anyone to read but the other (POST, PUT, DELETE) are protected with Basic Auth, the default user and password is admin:password and should be changed before deploying!
- PUT requests are supported but does not differ from POST requests, this is by design to keep everything simple, when updating an object, the whole object is passed. Only POST will be documented but they work the same.
- To be consistent, all POST requests should contain JSON data (except the media exception). If the parameters expected are `name` and `email`, the data would look like this:

```json
{
    "name": "John Doe",
    "email": "jdoe@example.com"
}
```


#### `/api/songs`

##### GET

Get all songs, a simple list with only the basic information

**Response codes**

| Response code | Reason                           |
|---------------|----------------------------------|
| 200           | Success                          |

Example response:
```json
[
    {
        "uuid": "0e9e41ca-d1cc-43f6-86b4-b20462010bcb",
        "name": "Feta fransyskor",
        "melody": "Tomtarnas julmarch"
    },
    {
        "uuid": "986475e0-2ab5-47e4-9b16-31bb02d9f0a3",
        "name": "Helan går",
        "melody": "Helan går"
    },
    ...
]
```

---

#### `/api/song/:uuid`

##### GET

Get detailed information about a song

**Response codes**

| Response code | Reason                           |
|---------------|----------------------------------|
| 200           | Success                          |
| 400           | Uuid not provided                |
| 404           | Song not found                   |

Response on success:
```json
{
    "uuid": "986475e0-2ab5-47e4-9b16-31bb02d9f0a3",
    "name": "Helan går",
    "lyrics": "Helan går,\nsjung hopp faderallan lallan lej...",
    "melody": "Helan går",
    "comment": "En klassisk snapsvisa",
    "categories": [
    	{
            "uuid": "0847e2b9-027d-4dca-bd6a-f4fa035be8be",
            "name": "Snapsvisor",
            "slug": "snapsvisor"
        }
    ],
    "media": [
        {
            "hash": "f6c4078137ac452c201dd9c023d41b072cea4e23a822f471bd0d62feb2ac6d4d",
            "mime": "audio/x-m4a",
            "description": "I-bandet Sångarstriden 2050"
        }
    ]
}
```


##### POST

**Parameters**

| Parameter    | Type          | Description                       | Required? |
|--------------|---------------|-----------------------------------|-----------| 
| `name`       | string        | The name of the song              | Yes       |
| `lyrics`     | string        | The lyrics to the song            | Yes       |
| `melody`     | string        | Melody of the song if any         | No        |
| `comment`    | string        | Comment on song                   | No        |
| `categories` | array[string] | Categories this song belongs in (only the uuids)  | No        |
| `media`      | array[string] | Media objects linked to this song (only the hashes) | No        |

Example request body:
```json
{
    "name": "Helan går",
    "lyrics": "Helan går,\nsjung hopp faderallan lallan lej...",
    "melody": "Helan går",
    "comment": "En klassisk snapsvisa",
    "categories": [
    	"0847e2b9-027d-4dca-bd6a-f4fa035be8be"
    ],
    "media": [
   	    "f6c4078137ac452c201dd9c023d41b072cea4e23a822f471bd0d62feb2ac6d4d"
    ]
}
```

**Response codes**

| Response code | Reason                           |
|---------------|----------------------------------|
| 201           | Song created                     |
| 400           | A requred parameter not provided |
| 401           | Unauthorized (Basic Auth)        |
| 404           | Song not found                   |

Response on success:
```json
{
    "uuid": "986475e0-2ab5-47e4-9b16-31bb02d9f0a3",
    "name": "Helan går",
    "lyrics": "Helan går,\nsjung hopp faderallan lallan lej...",
    "melody": "Helan går",
    "comment": "En klassisk snapsvisa",
    "categories": [
    	{
            "uuid": "0847e2b9-027d-4dca-bd6a-f4fa035be8be",
            "name": "Snapsvisor",
            "slug": "snapsvisor"
        }
    ],
    "media": [
        {
            "hash": "f6c4078137ac452c201dd9c023d41b072cea4e23a822f471bd0d62feb2ac6d4d",
            "mime": "audio/x-m4a",
            "description": "I-bandet Sångarstriden 2050"
        }
    ]
}
```

##### DELETE

Delete a song and unlik all media linked to it

**Response codes**

| Response code | Reason                    |
|---------------|---------------------------|
| 204           | Song deleted successfully |
| 400           | Uuid not provided         |
| 401           | Unauthorized (Basic Auth) |
| 404           | Song not found            |

---

#### `/api/categories`

##### GET

Get all categories

Example response:

```json
[
    {
        "uuid": "52375f2e-d824-4ef4-8198-6d9581661d5b",
        "name": "Måltidssånger",
        "slug": "maltidssanger"
    },
    {
        "uuid": "0847e2b9-027d-4dca-bd6a-f4fa035be8be",
        "name": "Snapsvisor",
        "slug": "snapsvisor"
    },
    ...
]
```

---

#### `/api/category/:uuid`

URL parameters:
- `uuid` - the uuid of the category, omitted or `new` when creating

##### GET

Get a specific category

| Response code | Reason             |
|---------------|--------------------|
| 200           | Success            |
| 400           | Uuid not provided  |
| 404           | Category not found |

Response on success:

```json
{
    "uuid": "52375f2e-d824-4ef4-8198-6d9581661d5b",
    "name": "Måltidssånger",
    "slug": "maltidssanger"
}
```

##### POST

Create a new category

**Parameters**

| Parameter | Type   | Description                              |
|-----------|--------|------------------------------------------|
| `name`    | string | The name of the category, must be unique |


**Response codes**

| Response code | Reason                    |
|---------------|---------------------------|
| 201           | Created successfully      |
| 400           | Name not provided         |
| 401           | Unauthorized (Basic Auth) |
| 409           | Name already exists       |

Response on success:
```json
{
    "uuid": "52375f2e-d824-4ef4-8198-6d9581661d5b",
    "name": "Måltidssånger",
    "slug": "maltidssanger"
}
```

##### DELETE

Delete a category and remove all links to it from songs

**Response codes**

| Response code | Reason                    |
|---------------|---------------------------|
| 204           | Success                   |
| 400           | Uuid not provided         |
| 401           | Unauthorized (Basic Auth) |
| 404           | Category not found        |

---

#### `/api/media/:hash`

URL parameters:
- `hash` - the file hash, omitted or set to `new` when creating new.

##### GET

Get the media file with the specified hash. The file is served normally

##### POST

Create a new media file (upload). Note: This request must be sent as a `multipart/form-data` to support file uploads. The max size of the uploaded file is 8MB.

**Parameters**

| Parameter     | Type   | Description               |
|---------------|--------|---------------------------|
| `description` | string | Description of media file |
| `audiofile`   | file   | Audio file to be uploaded |

**Response codes**

| Response code | Reason                     |
|---------------|----------------------------|
| 201           | File uploaded successfully |
| 401           | Unauthorized (Basic Auth)  |

Response on success:

```json
{
    "hash": "f6c4078137ac452c201dd9c023d41b072cea4e23a822f471bd0d62feb2ac6d4d",
    "mime": "audio/x-m4a",
    "description": "I-bandet Sångarstriden 2050"
}
```

##### DELETE

Delete the specified media file

**Response codes**

| Response code | Reason                    |
|---------------|---------------------------|
| 204           | File deleted successfully |
| 400           | Hash not provided         |
| 401           | Unauthorized (Basic Auth) |
| 404           | File not found            |

---

#### `/api/changepassword`

Change the admin password

##### POST

**Parameters**

| Parameter      | Type   | Description             |
|----------------|--------|-------------------------|
| `new_password` | string | The new password to set |

**Response codes**

| Response code | Reason                        |
|---------------|-------------------------------|
| 204           | Password updated successfully |
| 400           | Bad parameters                |
| 401           | Unauthorized (Basic Auth)     |
