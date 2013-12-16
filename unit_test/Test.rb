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
        inputTextOnAC :statovarius
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length == 6
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'track').length == 6
    end

    def autocompleteUniqueElements(text)
        loginLocal()
        @browser.text_field(:class => 'search').focus
        @browser.text_field(:class => 'search').set text
        Watir::Wait.until {
            @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length > 0
        }
        ['album', 'track'].each do |type|
            titles = Array.new
            @browser.element(:class => 'ui-autocomplete').elements(:class, type).each do |li|
                if li.element(:class => 'description').span.exists?
                    t = li.element(:class => 'description').span.text
                    assert(titles.include?(t) == false, titles.join(', ') + ' do have ' + t)
                    titles << t
                end
            end
        end
    end

    def testAutocompleteUniqueElements1
        autocompleteUniqueElements('rolling stones aftermath')
    end

    def testAutocompleteUniqueElements2
        autocompleteUniqueElements('coldplay')
    end

    def testAutocompleteUniqueElements3
        autocompleteUniqueElements('u2 sweetest')
    end

    def testClassicSearchView
        selectSearchMode("Classic View")
        selectSearchMode("IncBoard")
        selectSearchMode("Classic View")
    end

    def testIncBoard
        loginLocal()
        selectSearchMode("IncBoard")
        inputTextOnAC('stratov')
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'album').length == 6
        assert @browser.element(:class => 'ui-autocomplete').elements(:class, 'track').length == 6
        @browser.execute_script("$('.ui-autocomplete a').trigger('click')")
        Watir::Wait.until {
            @browser.elements(:class, 'incboard-cell').length > 0
        }
        cell = @browser.elements(:class, 'incboard-cell').first
        cell.hover
        cell.click
        @browser.div(:id => 'screen-music').click
        Watir::Wait.until {
            'Playlist: ' + cell.attribute_value(:name) == @browser.div(:class => 'jp-title').li.text
        }
        checkPlaylistLoaded
        
        assert 'Playlist: ' + cell.attribute_value(:name) == @browser.div(:class => 'jp-title').li.text
    end
end

class Player < Base
    def testRepeatAndShuffle
        selectSearchMode('IncBoard')
        checkPlaylistLoaded
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

    def testReloadTheSamePlaylist
        loginLocal()
        selectSearchMode("IncBoard")
        inputTextOnAC :stratovarius
        acSet = @browser.element(:class => 'ui-autocomplete')
        assert acSet.elements(:class, 'album').length == 6
        assert acSet.elements(:class, 'track').length == 6
        acSet.elements(:class, 'album').last.click
        Watir::Wait.until {
            @browser.elements(:class, 'incboard-cell').length > 0
        }

        currentAlbumId = 0
        currentAlbumName = ''
        @browser.elements(:class, 'incboard-cell').each {
            |ele|
            if ele.attribute_value('albumid') != nil
                currentAlbumId = ele.attribute_value('albumid')
                currentAlbumName = ele.attribute_value('name')
                ele.click
                break
            end
        }
        Watir::Wait.until {
            @browser.execute_script('return myPlaylist.name') == currentAlbumName
        }
        @browser.refresh
        Watir::Wait.until {
            @browser.execute_script('return myPlaylist.name') == currentAlbumName
        }

        assert @browser.execute_script('return myPlaylist.name') == currentAlbumName
        assert @browser.execute_script('return myPlaylist.type') == 'album'
    end

end

class OfflineEnv < Base
    def testArtistSearch
        searchOffline
        artistEle = @browser.elements(:class, 'ui-autocomplete').first.element(:class => 'artist', :class => 'ui-menu-item')
        assert artistEle.exists?
        assert artistEle.visible?
        artistEle.click
        @browser.div(:class => 'collection-info').wait_until_present

        assert @browser.div(:class => 'collection-info').exists?
        assert @browser.div(:class => 'similarity-list').exists?
        assert @browser.elements(:class, 'item-square').length > 4
    end

    def testAlbumSearch
        searchOffline
        albumEle = @browser.ul(:class => 'ui-autocomplete').elements(:class => 'album').last
        assert albumEle.exists?
        assert albumEle.visible?
        albumEle.click
        checkPlaylistLoaded
        assert @browser.div(:class => 'collection-info').exists?
        assert @browser.div(:class => 'cover').exists?
        assert @browser.div(:class => 'similarity-list').exists?
        assert @browser.elements(:class, 'item-square').length > 4
    end
end
