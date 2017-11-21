var gulp = require("gulp");

var del = require("del");
var fs = require("fs");
var gzip = require("gulp-gzip");
var newer = require("gulp-newer");
var preservetime = require("gulp-preservetime");
var replace = require("gulp-replace");


var path_src = "./src/www";
var path_dst = "./build/www";
var path_buildroot = "./build";



// Функции-обёртки для сокращения дубликации кода

// Копирует файлы, если они новее уже существующих, и сохраняет время их изменения
function copyWrapper(from, base, to, pipefunc = null, extra = [], map = function(p){ return p; }) {
    var pipe = gulp.src(from, {base: base})
    .pipe(newer({dest: to, extra: extra, map: map}));

    if (typeof(pipefunc) === "function")
        pipe = pipefunc(pipe);

    return pipe.pipe(gulp.dest(to))
    .pipe(preservetime());
}


// Задача сборки по умолчанию

gulp.task("default", [
    "build",
    "compress",
    "cachefolder"
]);


// Копирование и сборка файлов

gulp.task("build", [
    "build_general",
    "build_config",
    "build_libs"
]);


// Копирование основных файлов "как есть"

gulp.task("build_general", function() {
    return copyWrapper(
        [
            path_src + "/**",

            // Файл конфигурации
            "!" + path_src + "/include/config.php",

            // Системные файлы macOS
            "!._*",
            "!.DS_Store"
        ],
        path_src,
        path_dst
    );
});


// Сборка файла конфигурации

gulp.task("build_config", function() {
    var config = require(path_buildroot + "/config.json");

    return copyWrapper(
        path_src + "/include/config.php",
        path_src + "/include",
        path_dst + "/include",
        function(pipe) {
            return pipe
            .pipe(replace("PWD_ANON", config.password_anon))
            .pipe(replace("PWD_ADMIN", config.password_admin));
        },
        path_buildroot + "/config.json"
    )
});


// Сборка библиотек

gulp.task("build_libs", [
    "build_libs_bootstrap",
    "build_libs_jquery",
    "build_libs_popper",
    "build_libs_composer"
]);


// Bootstrap

gulp.task("build_libs_bootstrap", [
    "build_libs_bootstrap_css",
    "build_libs_bootstrap_js"
]);

gulp.task("build_libs_bootstrap_css", function() {
    return copyWrapper(
        "./node_modules/bootstrap/dist/css/bootstrap.min.css*",
        "./node_modules/bootstrap/dist/css",
        path_dst + "/css"
    );
});

gulp.task("build_libs_bootstrap_js", function() {
    return copyWrapper(
        "./node_modules/bootstrap/dist/js/bootstrap.min.js",
        "./node_modules/bootstrap/dist/js",
        path_dst + "/js"
    );
});


// jQuery

gulp.task("build_libs_jquery", function() {
    return copyWrapper(
        "./node_modules/jquery/dist/jquery.min.js",
        "./node_modules/jquery/dist",
        path_dst + "/js"
    );
});


// Popper.js

gulp.task("build_libs_popper", function() {
    return copyWrapper(
        "./node_modules/popper.js/dist/popper.min.js",
        "./node_modules/popper.js/dist",
        path_dst + "/js"
    );
});


// PHP-библиотеки из системы Composer

gulp.task("build_libs_composer", function() {
    return copyWrapper(
        [
            "./vendor/**",

            // системные файлы macOS
            "!._*",
            "!.DS_Store"
        ],
        "./",
        path_dst + "/include"
    );
});


// Сжатие файлов

gulp.task("compress", ["build"], function() {
    return copyWrapper(
        [
            path_dst + "/css/**/*.css*",
            path_dst + "/js/**/*.js"
        ],
        path_dst,
        path_dst,
        function(pipe) {
            return pipe.pipe(
                gzip({ level: 9 })
            );
        },
        [],
        function(p){ return p + '.gz'; }
    )
});


// Создание папки для кеша отрендеренных страниц

gulp.task("cachefolder", ["build"], function(cb) {
    fs.mkdir(path_dst + "/cache", 0o770, function(err){cb();});
});
