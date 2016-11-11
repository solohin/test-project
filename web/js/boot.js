require(
    {
        paths: {
            //jquery: "../lib/js/jquery-3.1.1.min",
            //Materialize: "../lib/js/materialize.min",
            handlebars: "../lib/js/handlebars-v4.0.5",
            //hammerjs: "../lib/js/hammer.min",
            //velocity: "../lib/js/velocity.min"
        },
        shim: {
            handlebars: {
                exports: 'Handlebars'
            }
            //Materialize: {
            //    deps: ['jquery', 'hammerjs', 'velocity'],
            //    exports: 'Materialize'
            //}
        }
    }, ["app", 'handlebars'], function (app) {
        app.init();
    }
);