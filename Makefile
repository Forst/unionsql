# Полная сборка проекта
build/unionsql.tar.xz: build/www.tar build/database.sql build/Dockerfile build/mysql_init.sh
	cp src/supervisord.conf build/supervisord.conf
	cp src/nginx-default build/nginx-default
	cp src/mysql_start.sh build/mysql_start.sh

	tar -cJf build/unionsql.tar.xz \
		build/Dockerfile \
		build/supervisord.conf \
		build/www.tar \
		build/nginx-default \
		build/database.sql \
		build/mysql_init.sh \
		build/mysql_start.sh


# Корень веб-сервера
build/www.tar: bootstrap build/passwords.sed
	$(call movepwd,config.json)
	npx gulp
	tar -cf build/www.tar -C build www


# Дамп базы данных
build/database.sql: build/passwords.sed
	$(call movepwd,database.sql)


# Сценарий развёртывания контейнера
build/Dockerfile: build/passwords.sed
	$(call movepwd,Dockerfile)


# Сценарий инициализации содержимого базы данных
build/mysql_init.sh: build/passwords.sed
	$(call movepwd,mysql_init.sh)
	chmod u+x build/mysql_init.sh


# Загрузка зависимостей
bootstrap: node_modules/ vendor/ build/


# Генерация паролей базы данных
build/passwords.sed: build/
	$(call getpasswd,admin)
	$(call getpasswd,anon)
	$(call getpasswd,hmac)
	$(call getpasswd,root)

	touch build/passwords.sed
	chmod 600 build/passwords.sed
	@echo "s/PWD_ADMIN/$(password_admin)/g" >> build/passwords.sed
	@echo "s/PWD_ANON/$(password_anon)/g" >> build/passwords.sed
	@echo "s/PWD_HMAC/$(password_hmac)/g" >> build/passwords.sed
	@echo "s/PWD_ROOT/$(password_root)/g" >> build/passwords.sed



# Завимисости NPM
node_modules/:
	npm install yarn
	npx yarn


# Зависимости PHP
vendor/:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	php composer.phar install


# Целевая папка сборки
build/:
	mkdir -p build


##########


# Копирование файла с безопасными правами и подмена плейсхолдеров на пароли
define movepwd
touch build/$1
chmod 600 build/$1
sed -f build/passwords.sed src/$1 > build/$1
endef


# Функция генерации пароля
define getpasswd
$(shell if [ ! -f "build/password_$1" ]; then \
	touch "build/password_$1"; \
	chmod 600 "build/password_$1"; \
	dd if=/dev/random count=1 bs=12 2>/dev/null | base64 | tr '+/' '-_' > "build/password_$1"; \
fi )
$(eval password_$1 = $(shell cat "build/password_$1"))
endef


##########


# Очистка собранных файлов
clean:
	rm -rf build


# Очистка собранных файлов и всех зависимостей
distclean: clean
	rm -f composer.phar
	rm -f composer-setup.php
	rm -rf vendor
	rm -rf node_modules


# Обновление всех зависимостей
upgrade: bootstrap upgrade_npm upgrade_composer
	npx gulp build_libs


# Обновление зависимостей NPM
upgrade_npm: node_modules/
	npx yarn upgrade


# Обновление зависимостей PHP
upgrade_composer: vendor/
	php composer.phar update


.PHONY: all bootstrap clean distclean upgrade upgrade_npm upgrade_composer
