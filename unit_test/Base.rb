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

    def setMute
        switch = @browser.element(:class, 'jp-mute').visible? == false
        if switch
            @browser.element(:id, 'screen-music').click
            Watir::Wait.until {
                @browser.element(:class, 'jp-mute').visible?
            }
        end
        @browser.element(:class, 'jp-mute').click

        if switch
            @browser.element(:id, 'screen-search').click
            Watir::Wait.until {
                @browser.element(:id, 'jp_container_1').visible? == false
            }
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

        url = @browser.url;

        if ('IncBoard' == modeName && url.index('/index/incboard') ) || 'Classic View' == modeName
            return
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
            slide = 0
        else
            slide = 1
        end

        id = "screen-" + screen
        @browser.div(:id => id).click
        Watir::Wait.until {
            slide == @browser.execute_script("return window.swiper.activeIndex")
        }

        Watir::Wait.until {
            false == @browser.execute_script("return window.swiper.lock")
        }
    end

    def checkPlaylistLoaded
        Watir::Wait.until {
            4 == @browser.execute_script("return $('#jquery_jplayer_1').data('jPlayer').status.readyState")
        }
        assert @browser.elements(:class => 'playlist-row').length >= 1
        assert 'undefined' != @browser.execute_script('return typeof window.myPlaylist.id');
        assert 'undefined' != @browser.execute_script('return typeof window.myPlaylist.name');
    end

    def refresh
        @browser.refresh
        if @browser.element(:id => 'jquery_jplayer_1').exists?
            checkPlaylistLoaded
        end
    end

    def searchOffline
        @browser.text_field(:id => 'q').focus
        @browser.text_field(:id => 'q').set 'stratovarius'
        Watir::Wait.until {
            @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length >= 1
        }

        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'artist').length >= 1
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length >= 1
    end

    def inputTextOnAC(text)
        ac = @browser.text_field(:class => 'search')
        ac.focus
        ac.set 'stratova'
        Watir::Wait.until {
            @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length == 6
        }
    end

    def playlistShuffle(on)
       if on
           button = @browser.element(:class => 'jp-shuffle')
       else
           button = @browser.element(:class => 'jp-shuffle-off')
       end 

       if button.visible?
           button.click
       end

       Watir::Wait.until {
            @browser.execute_script("return window.myPlaylist.shuffledLock") == false
       }
    end

    def playlistRepeat(on)
       if on
           button = @browser.element(:class => 'jp-repeat')
       else
           button = @browser.element(:class => 'jp-repeat-off')
       end 

       if button.visible?
           button.click
       end

       Watir::Wait.until {
            @browser.execute_script("return window.myPlaylist.loopLock") == false
       }
    end

end
