require 'rubygems'
require 'test/unit'
require 'watir'
require 'watir-webdriver'

class BootstrapMessage < Test::Unit::TestCase
    def setup
        @browser = Watir::Browser.new :chrome
        @browser.goto 'http://amuzi.localhost'
    end

    def teardown
        @browser.close
    end

    def testClose
        @browser.execute_script("$.bootstrapMessage('blabla', 'error');");
        @browser.a(:class => 'close').click
        assert @browser.execute_script("$('.close').parent().parent().css('display')" == 'none');
    end
end


