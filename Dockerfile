# イメージを取得
FROM php:8.3.3-apache

# 独自のphp.iniファイル(PHPの設定ファイル)をコンテナ内の/usr/local/etc/php/ディレクトリにコピー
COPY php.ini /usr/local/etc/php/

# プロジェクトのhtmlディレクトリの内容をコンテナ内の/var/www/htmlにコピー
COPY html/ /var/www/html/

# apache-config.confをコンテナの適切な場所にコピー
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# 必要なパッケージやPHPの拡張モジュールをインストール
RUN apt-get update && apt-get install -y \
	git \
	curl \
	zip \
	unzip \
	libpq-dev \ 
    && docker-php-ext-install pdo_pgsql

# Apacheのmod_rewriteモジュールを有効にする
RUN a2enmod rewrite

# 作業ディレクトリを設定
WORKDIR /var/www

# デフォルトサイトを再有効化して新しい設定を適用
RUN a2ensite 000-default