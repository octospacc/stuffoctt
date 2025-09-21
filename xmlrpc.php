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
use PhpXmlRpc\Response;
use PhpXmlRpc\Value;

const POST_PUBLISH_OFFSET = 1000000;
const POST_DRAFT_OFFSET = 2000000;
const POST_FUTURE_OFFSET = 3000000;

const PAGE_PUBLISH_OFFSET = 4000000;
const PAGE_DRAFT_OFFSET = 5000000;

const OFFSETS = [
    'POST' => [
        'publish' => POST_PUBLISH_OFFSET,
        'draft' => POST_DRAFT_OFFSET,
        'future' => POST_FUTURE_OFFSET,
    ],
    'PAGE' => [

    ],
];

const OFFSETS_LIST = [
    POST_PUBLISH_OFFSET => ['POST', 'publish'],
    POST_DRAFT_OFFSET => ['POST', 'draft'],
    POST_FUTURE_OFFSET => ['POST', 'future'],
    PAGE_PUBLISH_OFFSET => ['PAGE', 'publish'],
    PAGE_DRAFT_OFFSET => ['PAGE', 'draft'],
];

function checkAuth($user, $pass) {
    $user_enc = user('encryption', $user);
    $user_pass = user('password', $user);
    $user_role = user('role', $user);

    if (is_null($user_enc) || is_null($user_pass) || is_null($user_role)) return false;

    if ($user_enc === 'password_hash')
        return password_verify($pass, $user_pass);

    return false;
}

function throwAuth() {
    sleep(3);
    return new Response(0, 403, 'Wrong username or password.');
}

function arrayDefault($array, $key, $default) {
    if (!isset($array[$key])) $array[$key] = $default;
}

function applyFilter($array, $filter) {
    if ($filter)
        $array = array_intersect_key($array, array_flip($filter));
    return $array;
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

function getPostHtml($post) {
    $parts = explode('<div class="toc-wrapper"', $post->body);
    $left = array_shift($parts);
    $parts = explode('</div>', implode('<div class="toc-wrapper"', $parts));
    $right = implode('</div>', array_slice($parts, 3));
    return $left . $right;
}

function getPostById($post_id) {
    $offset = getOffsetFromId($post_id);
    $sources = getPostsOfStatus(OFFSETS_LIST[$offset][1]);
    $post_id = count($sources) - ($post_id - $offset) + 1;
    if ($post_id > 0)
        return get_posts($sources, $post_id, 1)[0];
}

function getPostsOfStatus($type) {
    switch ($type) {
        case 'publish':
            return get_blog_posts();
        case 'draft':
            return get_draft_posts();
        case 'future':
            return get_scheduled_posts();
    }
}

function getOffsetFromId($id) {
    foreach (array_reverse(OFFSETS_LIST, true) as $offset => $types) {
        if ($id > $offset)
            return $offset;
    }
}

// function getTypesFromId($id) {
//     foreach (array_reverse(OFFSETS_LIST, true) as $offset => $types) {
//         if ($id > $offset)
//             return $types;
//     }
// }

function serializePostToMetaweblog($post, $id) {
    $created = new Value(date('Ymd\TH:i:s', $post->date), 'dateTime.iso8601');
    $modified = new Value(date('Ymd\TH:i:s', $post->lastMod), 'dateTime.iso8601');
    return [
        'postid' => $id,
        'title' => $post->title,
        'description' => getPostHtml($post),
        'link' => $post->url,
        'userid' => $post->author,
        'dateCreated' => $created,
        'date_created_gmt' => $created,
        'date_modified' => $modified,
        'date_modified_gmt' => $modified,
        'post_status' => 'publish',
    ];
}

function serializePostToWordpress($post, $id, $filter = null) {
    $created = new Value(date('Ymd\TH:i:s', $post->date), 'dateTime.iso8601');
    $modified = new Value(date('Ymd\TH:i:s', $post->lastMod), 'dateTime.iso8601');
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
        'post_content' => getPostHtml($post),
        'link' => $post->url,
        'terms' => [],
    ];
    return applyFilter($result, $filter);
    // if ($filter)
    //     $result = array_intersect_key($result, array_flip($filter));
    // return $result;
}

function blogger_getUsersBlogs($a, $user, $pass) {
    if (!checkAuth($user, $pass)) return throwAuth();

    return blogInfo($user);
}

function metaWeblog_getRecentPosts($i, $user, $pass, $number = null) {
    if (!checkAuth($user, $pass)) return throwAuth();

    // return [serializePostToMetaweblog(get_posts(null, 10, 1)[0], POST_PUBLISH_OFFSET)];
    $results = [];
    $status = 'publish';
    $sources = getPostsOfStatus($status);
    $id = OFFSETS['POST'][$status] + count($sources);

    foreach (get_posts($sources, 1, $number ?? 10) as $post)
        $results[] = serializePostToMetaweblog($post, $id--);

    return $results;
}

function metaWeblog_getPost($post_id, $user, $pass) {
    if (!checkAuth($user, $pass)) return throwAuth();

    // return serializePostToMetaweblog(get_posts(null, 10, 1)[0], POST_PUBLISH_OFFSET);
    return serializePostToMetaweblog(getPostById($post_id), $post_id);
}

function mt_getPostCategories($post_id, $user, $pass) {
    if (!checkAuth($user, $pass)) return throwAuth();

    // ~~~
}

function wp_getUsersBlogs($user, $pass) {
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

    $statuses = explode(',', $opts['post_status']);
    // if (!in_array('publish', $statuses)) return [];
    // if (in_array('trash'))
    // if ($opts['post_status'] === 'trash') return [];
    if (in_array('publish', $statuses))
        $status = 'publish';
    elseif (in_array('draft', $statuses))
        $status = 'draft';
    elseif (in_array('future', $statuses))
        $status = 'future';
    else
        return [];

    // foreach($statuses as $status) {
    //     $sources = getPostsOfStatus($status);
    //     if ($sources) {
    //         $results = [];
    //         $id = OFFSETS['POST'][$status] + count($sources) - $opts['offset'];
    //     }
    // }

    // number = 10
    // offset = 0
    // orderby = date
    // post_status = draft,pending
    // order = DESC

    // post_id  post_modified_gmt  post_status

    // $sources = get_blog_posts();
    $results = [];
    $sources = getPostsOfStatus($status);
    $id = OFFSETS['POST'][$status] + count($sources) - $opts['offset'];

    foreach (get_posts($sources, 1 + ($opts['offset'] / $opts['number']), $opts['number']) as $post) {
        $results[] = serializePostToWordpress($post, $id--, $filter);
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

    // $sources = get_blog_posts();
    // $post_id = count($sources) - ($post_id - POST_PUBLISH_OFFSET) + 1;
    // if ($post_id > 0) {
    //     return serializePostToWordpress(get_posts($sources, $post_id, 1)[0], $post_id + POST_PUBLISH_OFFSET);
    // }

    // $offset = getOffsetFromId($post_id);
    // $sources = getPostsOfStatus(OFFSETS_LIST[$offset][1]);
    // $post_id = count($sources) - ($post_id - $offset) + 1;
    // if ($post_id > 0)
    //     return serializePostToWordpress(get_posts($sources, $post_id, 1)[0], $post_id + $offset);

    return serializePostToWordpress(getPostById($post_id), $post_id);
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
