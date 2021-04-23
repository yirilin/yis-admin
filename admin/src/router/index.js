import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

//解决vue路由跳转错误
const originalPush = Router.prototype.push
Router.prototype.push = function push(location, onResolve, onReject) {
    if (onResolve || onReject) return originalPush.call(this, location, onResolve, onReject)
    return originalPush.call(this, location).catch(err => err)
}

const baseRouters = [{
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'login',
        component: () =>
            import ('@/view/login/login.vue')
    },
    {
        path: '/register',
        name: 'register',
        component: () =>
            import ('@/view/login/register.vue')
    }
]

// 需要通过后台数据来生成的组件

const createRouter = () => new Router({
    routes: baseRouters
})

const router = createRouter()

export default router