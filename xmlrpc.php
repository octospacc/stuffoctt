<?php
define('HTMLY', true);
require 'system/vendor/autoload.php';
config('source', 'config/config.ini');

spl_autoload_register(function ($class) {
    $prefix = 'PhpXmlRpc\\';
    $baseDir = __DIR__ . '/system/vendor/phpxmlrpc/phpxmlrpc/src/';
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    // Get the relative class name
    $relativeClass = substr($class, $len);
    // Replace namespace separators with directory separators
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    // Load the file if it exists
    if (file_exists($file)) require $file;
});

use PhpXmlRpc\Server;
// use PhpXmlRpc\Request;
use PhpXmlRpc\Response;
use PhpXmlRpc\Value;
//use PhpXmlRpc\Exception;

// Define your method
// function blogNewPost($methodName, $params, $appData)
// {
//     $title = $params[0]->scalarval();
//     $content = $params[1]->scalarval();

//     // You can add authentication or validation here

//     $message = "New post created: '$title' with content: '$content'";
//     return new Response(new Value($message));
// }

function checkAuth($user, $pass) {
    // return empty(session($user, $pass));

    $user_enc = user('encryption', $user);
    $user_pass = user('password', $user);
    $user_role = user('role', $user);

    if (is_null($user_enc) || is_null($user_pass) || is_null($user_role)) return false;

    if ($user_enc === 'password_hash') {
        return password_verify($pass, $user_pass);
    }

    return false;

    // throw new Exception('Wrong username or password.', 403);
    // throw new XmlRpcException('Wrong username or password.', 403);
}

function throwAuth() {
    sleep(3);
    return new Response(0, 403, 'Wrong username or password.');
}

function arrayDefault(&$array, $key, $default) {
    if (!isset($array[$key])) $array[$key] = $default;
}

function blogInfo($user) {
    return [[
        'isAdmin' => user('role', $user) === 'admin',
        // 'isPrimary' => true,
        'blogid' => '1',
        'blogName' => blog_title(),
        'url' => site_url(),
        'xmlrpc' => site_url() . 'xmlrpc.php',
    ]];
}

function serializePost($post, $id, $filter = []) {
    $created = new Value(date('Ymd\TH:i:s', $post->date), 'dateTime.iso8601');
    $modified = new Value(date('Ymd\TH:i:s', $post->lastMod), 'dateTime.iso8601');
    $parts = explode('<div class="toc-wrapper"', $post->body);
    $left = array_shift($parts);
    $parts = explode('</div>', implode('<div class="toc-wrapper"', $parts));
    $right = implode('</div>', array_slice($parts, 3));
    $result = [
        'post_id' => $id,
        'post_name' => $post->slug,
        'post_title' => $post->title,
        'post_date' => $created,
        'post_date_gmt' => $created,
        'post_modified' => $modified,
        'post_modified_gmt' => $modified,
        'post_author' => $post->author,
        'post_status' => 'publish',
        'post_content' => $left . $right, // $post->body,
        'link' => $post->url,
        'terms' => [],
    ];
    if ($filter)
        $result = array_intersect_key($result, array_flip($filter));
    return $result;
}

function blogger_getUsersBlogs($a, $user, $pass) {
    if (!checkAuth($user, $pass)) return throwAuth();

    return blogInfo($user);
}

function metaWeblog_getRecentPosts($i, $user, $pass, $number = null) {
    if (!checkAuth($user, $pass)) return throwAuth();

    // return [serializePost(get_posts(null, 10, 1)[0], 4611686018427387904)];
}

function metaWeblog_getPost($post_id, $user, $pass) {
    if (!checkAuth($user, $pass)) return throwAuth();

    // return serializePost(get_posts(null, 10, 1)[0], 4611686018427387904);
}

function mt_getPostCategories($post_id, $user, $pass) {
    if (!checkAuth($user, $pass)) return throwAuth();


}

function wp_getUsersBlogs($user, $pass) {
    // if (!checkAuth($u, $p)) return null;
    // throw new Exception('Wrong username or password.', 403);
    // ensureAuth($u, $p);
    if (!checkAuth($user, $pass)) return throwAuth();

    return blogInfo($user);
}

function wp_getProfile($i, $user, $pass) {
    if (!checkAuth($user, $pass)) return throwAuth();

    $author = get_author($user)[0];
    return [
        'user_id' => $user,
        'username' => $user,
        // 'nickname' => '',
        // 'nicename' => '',
        // 'first_name' => '',
        // 'last_name' => '',
        // 'display_name' => '',
        // 'email' => '',
        'bio' => $author->about,
        'url' => $author->url,
        // 'registered' => '', // dateTime.iso8601
        'roles' => [user('role', $user) === 'admin' ? 'administrator' : ''],
    ];
}

function wp_getPosts($i, $user, $pass, $opts = [], $filter = null) {
    if (!checkAuth($user, $pass)) return throwAuth();

    arrayDefault($opts, 'number', 10);
    arrayDefault($opts, 'offset', 0);
    arrayDefault($opts, 'post_status', '');

    $status = explode(',', $opts['post_status']);
    if (!in_array('publish', $status)) return [];
    // if (in_array('trash'))
    // if ($opts['post_status'] === 'trash') return [];

    // number = 10
    // offset = 0
    // orderby = date
    // post_status = draft,pending
    // order = DESC

    // post_id  post_modified_gmt  post_status

    $sources = get_blog_posts();
    $results = [];
    // $id = 1 + $opts['offset'];
    $id = 4611686018427387904 + count($sources) - $opts['offset'];

    // var_dump(get_posts(null, 1, $opts['number']));
    foreach (get_posts($sources, 1 + ($opts['offset'] / $opts['number']), $opts['number']) as $post) {
        // $created = new Value(date('Ymd\TH:i:s', $post->date), 'dateTime.iso8601');
        // $modified = new Value(date('Ymd\TH:i:s', $post->lastMod), 'dateTime.iso8601');
        // $results[] = [
        //     'post_id' => $id--, // $id++,
        //     'post_title' => $post->title,
        //     'post_date' => $created,
        //     'post_date_gmt' => $created,
        //     'post_modified' => $modified,
        //     'post_modified_gmt' => $modified,
        //     'post_author' => $post->author,
        //     'post_status' => 'publish',
        // ];
        $results[] = serializePost($post, $id--, $filter);
    }

    // 'post_id'
    // 'post_title'
    // 'post_date'
    // 'post_date_gmt'
    // 'post_modified'
    // 'post_modified_gmt'
    // 'post_status'
    // 'post_type'
    // 'post_name'
    // 'post_author'
    // 'post_password'
    // 'post_excerpt'
    // 'post_content'
    // 'post_parent'
    // 'post_mime_type'
    // 'link'
    // 'guid'
    // 'menu_order'
    // 'comment_status'
    // 'ping_status'
    // 'sticky'
    // 'post_thumbnail' => [],
    // 'post_format'
    // 'terms' => [
        // 'term_id'
        // 'name'
        // 'slug'
        // 'term_group'
        // 'term_taxonomy_id'
        // 'taxonomy'
        // 'description'
        // 'parent'
        // 'count'
        // 'filter'
    //],
    // 'custom_fields' => [],

    return $results;
}

function wp_getPost($i, $user, $pass, $post_id) {
    if (!checkAuth($user, $pass)) return throwAuth();

    // find_post($year, $month, $name)
    $sources = get_blog_posts();
    $post_id = count($sources) - ($post_id - 4611686018427387904) + 1;
    if ($post_id > 0) {
        return serializePost(get_posts($sources, $post_id, 1)[0], $post_id + 4611686018427387904);
    }
}

function wp_newPost($i, $user, $pass, $data) {
    if (!checkAuth($user, $pass)) return throwAuth();

    // post_title
    // post_content
    // post_name
    // post_date
    // post_date_gmt
    // post_thumbnail = 0
    // post_format = standard
    // post_type = post
    // post_status = publish|draft|...
    // post_excerpt
    // comment_status = open
}

function wp_editPost($i, $user, $pass, $post_id, $data) {
    if (!checkAuth($user, $pass)) return throwAuth();

    // post_title
    // post_content
    // terms
    //      category = []
    // post_tag = []
    // post_name
    // post_date
    // post_password
    // post_thumbnail
    // post_format
    // post_type
    // post_status
    // post_excerpt
    // post_date_gmt
}

$methods = [
    'blogger.getUsersBlogs',
    'metaWeblog.getRecentPosts', 'metaWeblog.getPost',
    'mt.getPostCategories',
    'wp.getUsersBlogs', 'wp.getProfile', 'wp.getOptions',
    'wp.uploadFile',
    'wp.getPage', 'wp.getPages',
    'wp.newPage', 'wp.editPage', 'wp.deletePage',
    'wp.getPost', 'wp.getPosts',
    'wp.newPost', 'wp.editPost', 'wp.deletePost',
    'wp.getTags',
    'wp.getCategories', 'wp.newCategory',
    'wp.getComment', 'wp.getComments', 'wp.getCommentStatusList',
    'wp.newComment', 'wp.editComment', 'wp.deleteComment',
];

// Register the method
// $dispatchMap = [
//     /*
//     'blog.newPost' => [
//         'function' => 'blogNewPost',
//         'signature' => [['string', 'string', 'string']],
//         'docstring' => 'Creates a new blog post'
//     ]
//     */
//     'wp.getProfile' => [],
//     'wp.getOptions' => [],
//     // 'wp.getPostFormats' => [],
//     // 'wp.getUserBlogs' => [],
//     'wp.getUsersBlogs' => [ 'function' => 'wpGetUsersBlogs', 'parameters_type' => 'phpvals' ],
//     // 'wp.getMediaLibrary' => [],
//     // 'wp.getMediaItem' => [],
//     'wp.uploadFile' => [],
//     'wp.getPage' => [],
//     'wp.getPages' => [],
//     'wp.newPage' => [],
//     'wp.editPage' => [],
//     'wp.deletePage' => [],
//     'wp.getPost' => [],
//     'wp.getPosts' => [],
//     'wp.newPost' => [],
//     'wp.editPost' => [],
//     'wp.deletePost' => [],
//     // 'wp.getTerm' => [],
//     // 'wp.getTerms' => [],
//     // 'wp.newTerm' => [],
//     // 'wp.editTerm' => [],
//     // 'wp.deleteTerm' => [],
//     'wp.getTags' => [],
//     'wp.getCategories' => [],
//     'wp.newCategory' => [],
//     'wp.getComment' => [],
//     'wp.getComments' => [],
//     'wp.getCommentStatusList' => [],
//     'wp.newComment' => [],
//     'wp.editComment' => [],
//     'wp.deleteComment' => [],
// ];

// Create and run the server
//$server = new Server($dispatchMap);
$server = new Server(array_reduce($methods, function ($map, $method) {
    $map[$method] = [
        'function' => str_replace('.', '_', $method),
        'parameters_type' => 'phpvals',
    ];
    return $map;
}, []));
//$server->setOption(Server::OPT_FUNCTIONS_PARAMETERS_TYPE, 'phpvals');
header('X-Robots-Tag: noindex');
$server->service();

/*
phpinfo();

// Create the server
$server = xmlrpc_server_create();

// Register a method
xmlrpc_server_register_method($server, "blog.newPost", "handleNewPost");

// Define the handler function
function handleNewPost($method, $params, $appData) {
    $title = $params[0];
    $content = $params[1];
    return "Post titled '$title' created with content: $content";
}

// Read the raw POST data
$request = file_get_contents("php://input");

// Process the request
$response = xmlrpc_server_call_method($server, $request, null);

// Send the response
header("Content-Type: text/xml");
echo $response;

// Clean up
xmlrpc_server_destroy($server);

*/

//echo 123;
