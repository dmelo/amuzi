load 'Base.rb'

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
        sleep 1
        assert @browser.url.index('/index/incboard') == nil

        selectSearchMode("IncBoard")
        sleep 1
        assert @browser.url.index('/index/incboard') != nil

        selectSearchMode("Classic View")
        sleep 1
        assert @browser.url.index('/index/incboard') == nil
    end
end

