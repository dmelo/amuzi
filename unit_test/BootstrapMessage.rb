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


