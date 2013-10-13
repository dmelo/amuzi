require 'rubygems'
require 'test/unit'
require 'watir'
require 'watir-webdriver'
require 'watir-webdriver/wait'
require 'headless'

class Base < Test::Unit::TestCase
    def setup
        if ENV['HEADLESS']
            @headless = Headless.new
            @headless.start
        end
        @browser = Watir::Browser.new :chrome
        @browser.goto 'http://amuzi.localhost'
    end

    def teardown
        @browser.close
        if ENV['HEADLESS']
            @headless.destroy
        end
    end

    def loginLocal
       @browser.text_field(:name => 'email').set 'dmelo87@gmail.com'
       @browser.text_field(:name => 'password').set '123456'
       @browser.button(:name => 'submit').click
       assert @browser.a(:id => 'userEmail').text != ''
    end

    def selectSearchMode(modeName)
        if @browser.a(:id => 'userEmail').text == ''
            loginLocal()
        end

        @browser.a(:href => '/user', :class => 'loadModal').click
        Watir::Wait.until {
            @browser.select_list(:id => 'view').exists?
        }
        @browser.select_list(:id => 'view').select modeName
        @browser.form(:id => 'usersettings').button(:name => 'submit').click
        sleep 1
        if 'IncBoard' == modeName
            assert @browser.url.index('/index/incboard') != nil
        else
            assert @browser.url.index('/index/incboard') == nil
        end
    end

    def clickScreen(screen)
        slide = ''
        if "search" == screen
            slide = 'next'
        else
            slide = 'prev'
        end

        id = "screen-" + screen
        waitId = 'slide-' + slide
        @browser.div(:id => id).click
        Watir::Wait.until {
            1 == @browser.execute_script("return $('." + waitId + ".active').length")
        }
    end

    def refresh
        @browser.refresh
        Watir::Wait.until {
            4 == @browser.execute_script("return $('#jquery_jplayer_1').data('jPlayer').status.readyState")
        }
    end
end
