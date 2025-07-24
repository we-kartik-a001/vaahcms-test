let routes= [];
let routes_list= [];

import List from '../pages/tags/List.vue'
import Form from '../pages/tags/Form.vue'
import Item from '../pages/tags/Item.vue'

routes_list = {

    path: '/tags',
    name: 'tags.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'tags.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'tags.view',
            component: Item,
            props: true,
        }
    ]
};

routes.push(routes_list);

export default routes;

