import Vue from 'vue'
import Router from 'vue-router'
import HelloWorld from '@/components/HelloWorld'
import money from '@/components/money'
import payRecord from '@/components/pay_record'
import pRepay from '@/components/p_repay'
import pRepayRecord from '@/components/p_repay_cord'
import changepsw from '@/components/changepsw'
import login from '@/components/login'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/:name',
      name: '首页',
      component: HelloWorld,
      // redirect: 'HelloWorld'
    },
    {
      path: '/money/:name',
      name: '资金池资金查询',
      component: money
    }
    ,
    {
      path: '/pay_record/:name',
      name: '充值记录查询',
      component: payRecord
    }
    ,
    {
      path: '/p_repay/:name',
      name: '代付记录查询',
      component: pRepay
    }
    ,
    {
      path: '/p_repay_cord/:name',
      name: '代付批次管理',
      component: pRepayRecord
    }
    ,
    {
      path: '/p_repay_cord/:name',
      name: '修改支付密码',
      component: changepsw
    }
    ,
    {
      path: '/changepsw/:name',
      name: '修改登陆密码',
      component: changepsw
    }
    ,
    {
      path: '/login/:name',
      name: '登录',
      component: login
    }
  ]
})
