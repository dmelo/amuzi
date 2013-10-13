load 'Base.rb'

class BootstrapMessage < Base
    def testClose
        @browser.execute_script("$.bootstrapMessage('blabla', 'error');");
        Watir::Wait.until {
            @browser.execute_script("return $('.close').parent().css('display')") == 'block';
        }
        @browser.link(:class => 'close').click
        Watir::Wait.until {
            @browser.execute_script("return $('.close').parent().css('display')") == 'none';
        }
        assert @browser.execute_script("return $('.close').parent().css('display')") == 'none';
    end

    def testMultipleMessages
        status = ['success', 'info', 'warning', 'error']
        command = '';
        status.each {
            |x|
            command = command << "$.bootstrapMessageAuto('" << x << "', '" << x << "');";
        }
        @browser.execute_script(command);

        status.each {
            |x|
            Watir::Wait.until {
                @browser.execute_script("return $('.alert-" << x << "').length") == 1
            }

            assert @browser.execute_script("return $('.alert-" << x << "').length") == 1
            assert @browser.execute_script("return $('.alert-" << x << " p').html()") == x
        }
    end
end

class Search < Base
    def testAutocompleteLoad
        loginLocal()
        @browser.text_field(:class => 'search').focus
        @browser.text_field(:class => 'search').set 'stratova'
        Watir::Wait.until {
            @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length == 6
        }
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length == 6
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'track').length == 6
    end

    def testClassicSearchView
        selectSearchMode("Classic View")
        selectSearchMode("IncBoard")
        selectSearchMode("Classic View")
    end

    def testIncBoard
        selectSearchMode('IncBoard')
        @browser.text_field(:class => 'search').focus
        @browser.text_field(:class => 'search').set 'stratova'
        Watir::Wait.until {
            @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length == 6
        }
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length == 6
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'track').length == 6
        @browser.execute_script("$('.ui-autocomplete a').trigger('click')")
        Watir::Wait.until {
            @browser.elements(:class, 'incboard-cell').length > 0
        }
        cell = @browser.elements(:class, 'incboard-cell').first
        cell.hover
        cell.div(:class => 'play').a.click
        @browser.div(:id => 'screen-music').click
        Watir::Wait.until {
            'Playlist: ' + cell.attribute_value(:name) == @browser.div(:class => 'jp-title').li.text
        }
        
        assert 'Playlist: ' + cell.attribute_value(:name) == @browser.div(:class => 'jp-title').li.text
    end
end

class Player < Base
    def testRepeatAndShuffle
        selectSearchMode('IncBoard')
        repeatOff = @browser.element(:class => 'jp-repeat-off')
        repeatOn = @browser.element(:class => 'jp-repeat')

        shuffleOff = @browser.element(:class => 'jp-shuffle-off')
        shuffleOn = @browser.element(:class => 'jp-shuffle')

        clickScreen("music")
        if repeatOff.visible?
            repeatOff.click
        end

        if shuffleOff.visible?
            shuffleOff.click
        end

        refresh
        clickScreen("music")

        assert repeatOn.visible?
        assert shuffleOn.visible?

        repeatOn.click
        shuffleOn.click

        refresh
        clickScreen("music")

        assert repeatOff.visible?
        assert shuffleOff.visible?
    end
end
