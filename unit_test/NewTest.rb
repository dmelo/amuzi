load 'Base.rb'

class NewTest < Base
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


end
