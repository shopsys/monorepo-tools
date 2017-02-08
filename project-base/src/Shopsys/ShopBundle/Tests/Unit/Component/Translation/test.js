Shopsys.translator.trans('trans test');

Shopsys.translator.transChoice('transChoice test', 5);

Shopsys.translator.trans('trans test with domain', {}, 'testDomain');

Shopsys.translator.transChoice('transChoice test with domain', 5, [], 'testDomain');

Shopsys.translator.trans('concatenated' + ' ' + 'message');