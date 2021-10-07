# End points 
**USER**

| Method | Link |
- |Post  |`http://127.0.0.1:8000/api/register`|
- Return: status, code and user
- |Post  |`http://127.0.0.1:8000/api/login`|
- Return: status, code and Json web token
- |Post  |`http://127.0.0.1:8000/api/user/upload`|
- Require: JWT and image to update
- Return: status, code and user
- |Put    |`http://127.0.0.1:8000/api/user/update`|
- Require: JWT and Data to update
- Return: status, code and user
- |Get    |`http://127.0.0.1:8000/api/user/avatar/:idimage`|
- Return: status, code, and binary image
- |Get    |`http://127.0.0.1:8000/api/user/detail/:id`|
- Return: status, code and user


**Category**

| Method | Link |
- |Get |`http://127.0.0.1:8000/api/category/`|
- Return: status, code, all categories
- |Get |`http://127.0.0.1:8000/api/category/:id`|
- Return:status, code, single category by id
- |Post  |`http://127.0.0.1:8000/api/category/`|
- Require: JWT and data to create
- Return: status, code, create category
- |Delete |`http://127.0.0.1:8000/api/category/:id`|
- Require: JWT and id to delete
- Return:status, code, deleted category
- |Put    |`http://127.0.0.1:8000/api/category/:id`|
- Require: JWT and data to update
- Return: status, code, updated category


**Posts**

| Method | Link |
- |Get |`http://127.0.0.1:8000/api/Post/`|
- Return: status, code, all posts
- |Get |`http://127.0.0.1:8000/api/Post/:id`|
- Return: status, code, single post by id
- |Post  |`http://127.0.0.1:8000/api/Post/`|
- Require: JWT and data to create
- Return: status, code, create post
- |Delete |`http://127.0.0.1:8000/api/Post/:id`|
- Require: JWT and id to delete
- Return: status, code, deleted post
- |Put    |`http://127.0.0.1:8000/api/Post/:id`|
- Require: JWT and data to update
- Return: status, code, updated post
