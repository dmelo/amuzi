require 'rubygems'
require 'test/unit'
require 'watir'
require 'watir-webdriver'
require 'headless'

class BootstrapMessage < Test::Unit::TestCase
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


