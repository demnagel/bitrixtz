BX.ready(function(){

    BX.Shop = BX.Shop || {};
    BX.Shop.Listener = BX.Shop.Listener || {};

    if(typeof BX.Shop.Listener.ComparisonProducts == 'undefined') {
        /**
         * Класс реализует прослушку и обработку события по добавлению/удалению элемента в список сравнения, перерендеривает 
         * блоки (если их несколько) с кол-во товаров (допустим есть блоки с кол-вом товаров добавленных в сравнение)
         */
        BX.Shop.Listener.ComparisonProducts = {

            component: 'shop:comparsion.products',
            event: 'comparsion_products_change',
            componentMethod: 'edit',
            visibilityClass: 'd-none',
            countContainerSelector: '.comparsion_quantity_container',
            countSelector : '.js-comparsion_quantity',

            /**
             *  Скрывает/показывает блоки в зависимости от кол-ва
             * @param count
             */
            checkVisible: function(count){
                var self = this;

                if(count < 1){
                    var method = 'addClass';
                }
                else{
                    var method = 'removeClass';
                }

                $(this.countContainerSelector).each(function(index, elem){
                    $(elem)[method](self.visibilityClass);
                });
            },

            /**
             * Актуализация значения во всех блоках
             * @param count
             */
            setComparisonProductsCount: function(count){
                $(this.countSelector).each(function(index, elem){
                    $(elem).text(count);
                });
            },

            /**
             * Запрос в компонент
             * @param method
             * @param productId
             */
            runQuery: function(method, productId){
                var self = this;

                BX.ajax.runComponentAction(
                    self.component,
                    self.componentMethod,
                    {
                        mode:'class',
                        data: {
                            method: method,
                            productId: productId
                        }
                    }
                ).then(function(response){
                    if (response.status === 'success') {
                        self.setComparisonProductsCount(response.data.count);
                        self.checkVisible(response.data.count);
                    }
                }).catch(function (error) {
                    console.log('error: ' + error.response)
                });
            },

            /**
             * Прослушка события
             */
            listenEvent: function(){
                var self = this;
                BX.addCustomEvent(self.event, BX.proxy(self.runQuery, self));
            }
        };
    }

    // Инициализация прослушки нужного события
    BX.Shop.Listener.ComparisonProducts.listenEvent();

});