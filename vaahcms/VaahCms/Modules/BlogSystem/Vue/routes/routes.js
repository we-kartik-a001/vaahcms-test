let routes= [];

import dashboard from "./vue-routes-dashboard";
import blogs from "./vue-routes-blogs";
import tags from "./vue-routes-tags";
import categories from "./vue-routes-categories";


routes = routes.concat(dashboard);
routes = routes.concat(blogs);
routes = routes.concat(tags);
routes = routes.concat(categories);


export default routes;
