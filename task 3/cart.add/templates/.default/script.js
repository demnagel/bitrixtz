BX.ready(function(){

    BX.Shop = BX.Shop || {};
    BX.Shop.AddBasket = BX.Shop.AddBasket || {};

    BX.Shop.AddBasket = BX.Vue.create({
        el: '#items_wrapper',
        data: componentData || {},
        methods: {
            getName: function(id){
                let text = '';
                if(this.products[id]){
                    text = this.products[id].name;
                    this.showBtn = true;
                }
                else{
                    if(id){
                        text = BX.message('NOT_FOUND');
                        this.showBtn = false;
                    }
                }

                return text;
            },
            add : function() {
                this.fields.push('');
            },

            del : function(index) {
                this.fields.splice(index, 1);
                if(!this.idList.length){
                    this.showBtn = false;
                }
            },

            idList: function(){
                let idList = [];

                for(let i in this.fields){
                    if(this.products[this.fields[i]]){
                        idList.push(this.products[this.fields[i]].id);
                    }
                }

                return idList;
            },

            send: function() {
                
                let self = this;
                let idList = this.idList();

                if(this.runAjax){
                    return false;
                }

                if(idList.length){

                    this.runAjax = true;

                    BX.ajax.runComponentAction(
                        self.componentName,
                        self.componentMethod,
                        {
                            mode:'class',
                            data: {
                                productsId: idList
                            }
                        }
                    ).then(function(response){
                        if (response.status === 'success') {
                            BX.onCustomEvent('OnBasketChange');
                            if(response.data.status.success.length == idList.length){
                                alert(BX.message('SUCCESS'));
                            }
                            else{
                                alert(BX.message('ERR'));
                            }
                            self.runAjax = false;
                        }
                    }).catch(function (error) {
                        console.log('error: ' + error.response);
                        self.runAjax = false;
                    });

                    this.fields = [''];
                    this.showBtn = false;
                }
            }
        }
    });
});