#!/bin/bash

P=public/js

cat $P/stacktrace.js $P/facebook-connect.js $P/bootstrap.js $P/jquery.browser.min.js $P/jquery.jplayer.js $P/jplayer.playlist.js $P/jplayer.playlist.ext.js $P/themeswitcher.js $P/jquery-ui-1.9.2.custom.js $P/jquery.progressbar.js $P/jquery.placeholder.min.js $P/jquery.form.js $P/jquery.tableofcontents.js > $P/all-p1.js

cat $P/log.js $P/resultset.js $P/jquery.cookie.js $P/commands.js $P/jquery.bootstrapMessage.js $P/jquery.bootstrapLoadModal.js $P/idangerous.swiper.js $P/cssrule.js > $P/all-p2.js
