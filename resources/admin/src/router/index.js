import Vue from 'vue'
import Router from 'vue-router'
import HelloWorld from '@/components/HelloWorld'
import money from '@/components/money'
import payRecord from '@/components/pay_record'
import pRepay from '@/components/p_repay'
import pRepayRecord from '@/components/p_repay_cord'
import changepsw from '@/components/changepsw'
import log from '@/components/log.vue'
import login from '@/components/login'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/:name',
      name: '首页',
      component: HelloWorld
    },
    {
      path: '/money/:name',
      name: '审核管理',
      component: money
    }
    ,
    {
      path: '/pay_record/:name',
      name: '批次管理',
      component: payRecord
    }
    ,
    {
      path: '/p_repay/:name',
      name: '记录查询',
      component: pRepay
    }
    ,
    {
      path: '/log/:name',
      name: '日志查询',
      component: log
    }
    ,
    {
      path: '/:name',
      name: '会员列表',
      component: HelloWorld
    }
    ,
    {
      path: '/login:name',
      name: '登录',
      component: login
    }
  ]
})
