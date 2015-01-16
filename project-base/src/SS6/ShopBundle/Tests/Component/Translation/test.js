SS6.translator.trans('trans test');

SS6.translator.transChoice('transChoice test', 5);

SS6.translator.trans('trans test with domain', {}, 'testDomain');

SS6.translator.transChoice('transChoice test with domain', 5, [], 'testDomain');

SS6.translator.trans('concatenated' + ' ' + 'message');