# laravelのルート定義ファイル
`routes`ディレクトリ配下に設置する。routes/web.php中で定義されたルートは、ブラウザで定義したURLのルートを入力することでアクセスできる。
以下のルートは`https://user-name.test/name`からアクセスできる。

```
Route::get('/name', 'NameController@index');
```

# 使用できるHTTPメソッド

```php
Route::get($uri, $callback);      //データを取得
Route::post($uri, $callback);     //データを追加
Route::put($uri, $callback);      //データを更新
Route::patch($uri, $callback);    //PUTとほぼ同じ。一部を更新
Route::delete($uri, $callback);   //データを削除
Route::options($uri, $callback);  //使用可能メソッド一覧を表示
```

複数のメソッドを同時に使いたいときは`match`を用いる。

```php
Route::match(['get', 'post'], '/', function () {
    return 'ルーティングの勉強';
});
```
全メソッドにアクセス許可したいときは`any`を用いる。

```php
Route::any('foo', function () {
    return 'ルーティングの勉強';
});
```

# ルートパラメーター
### 必須パラメーター
URLの一部から値を取得したいときに使用する。例えば、ユーザーIDを取得したいとき。
getメソッドの第一引数に`{id}`のように書く。指定した値はfunction()の引数に渡される。
例えば、`https://user-name.test/user/12648`のURLがあった際に`$id`に`12648`のユーザーIDが代入される。

```php
Route::get('user/{id}', function ($id) {
    return 'User '.$id;
});
```

### 任意パラメーター
先程の必須パラメーターの項目で説明したコードだと、`https://user-name.test/user/`に接続した際にエラーになってしまう。
なぜなら{id}が**必須**のため。
そこで使用するのがidを省略できるパターン！！
getメソッドの第一引数に`{id?}`をいれ、function()の引数にデフォルトの値を入れる。
`https://user-name.test/user/`にアクセスすると、`User 100`と表示される。

```php
Route::get('user/{id?}', function ($id = 100){
    return 'User '.$id;
});
```

### 正規表現で制約を付ける
`https://user-name.test/user/null`というURLがあった場合、どうなるのか。。。
idの部分に数字以外の文字列が入った場合、idが登録されているDBはエラーを引き起こす可能性がある。
そこでidの部分に正規表現で制約をつけてあげれば解決できる！
以下の例の意味は、「idの部分で直前の一文字（数字の0~9）が一回以上繰り返されている文字列のURLのみアクセス可」になる。

```php
Route::get(`user/{id}`, function ($id){
    return 'User '.$id;
})->where('id', '[0-9]+');
```

# 名前付きルート
リダイレクトしたりする場合に便利。（例えば、ログインしていなかったらログインページにリダイレクトなど）
以下の例は、`https://user-name.test/user/profile`にアクセスすると、`UserController.php`の`getProfile()`が呼ばれる仕組みになっている。

```php
Route::get('user/profile', 'UserController@getProfile')->name('profile');
```

# ルートグループ
ユーザーの種類が「管理者/会員」などに分かれていて、さらにそのユーザーの種類ごとに複数のページがあるとします。
以下のようなページがある場合。

```
//管理者
https://user-name.test/admin/top
https://user-name.test/admin/user
https://user-name.test/admin/settings

//ユーザー
https://user-name.test/user/top
https://user-name.test/user/settings
https://user-name.test/user/profile
```
その際今までの学びを踏まえると、、、

```php
Route::get('admin/top', function(){});
Route::get('admin/user', function(){});
Route::get('admin/setting', function(){});
...
```
と複数書くことになる。。。。正直めんどくさい
ので、グループ化できたらよい！`group()`を使う(´・ω・｀)

グループ化には種類がある。
- middleware(ミドルウェア)
- namespace(ネームスペース)
- domain(サブドメイン)
- prefix(URLから始まる文字列)
- name(ルーティング名)

### middleware
グループの中の全ルートにミドルウェアを適用する。配列に入っている順番で実行される。

```php
Route::middleware('first', 'second')->group(function ()){
    Route::get('admin/top', function (){
        //firstとsecondミドルウェアを使用
    });
}
```

### namespace
これは、ネームスペースごとにグループ化する。
`namespace app/Http/Contollers/Admin;`のようなネームスペースが記述されているコントローラーが呼び出される。

```php
Route::namespace('Admin')->group(function (){
    Route::get('admin/top', 'AdminTopContoller@index');
});
```

### domain
サブドメインでグループ化する。
この場合、`https://user-name.test/admin/user/100`にはアクセスできない。サブドメインがないため。しかし、`https://www.user-name.test/admin/user/100`はアクセス可。

```php
Route::domain('{subdomain}.user-name.test')->group(function (){
    Route::get('admin/user/{id}', function ($subdomain, $id)
});
```

### prefix
URLの最初の文字列を指定できる。
この場合は、
`https://user-name.test/admin/top/
https://user-name.test/admin/user/
https://user-name.test/admin/settings/`

```php
Route::prefix('admin')->group(function (){
    Route::get('top', 'AdminTopContoller@index');
    Route::get('user', function (){});
    Route::get('settings' function (){});
});
```

### name
name()に指定した文字列は、ルート名の前につく。

```php
Route::name('admin.')->group(function (){
    Route::get('top', function (){
        //"admin.top"というルーティング名のルート
    })
});
```
