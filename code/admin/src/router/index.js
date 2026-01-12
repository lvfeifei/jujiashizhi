import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/Login',
            component: resolve => require(['@/page/Login.vue'], resolve),
        },
        {
            path: '/',
            component: resolve => require(['@/page/index.vue'], resolve),
            // redirect: '/index/NursingHome',
            redirect: '/index/zhaohuzhe_list',
        },
        {
            path: '/index',
            component: resolve => require(['@/page/index.vue'], resolve),
            redirect: '/index/zhaohuzhe_list',
            children: [
                // 养老院主页
                {
                    path: '/index/NursingHome',
                    component: resolve => require(['@/page/index/NursingHome.vue'], resolve)
                },
                //养老院管理列表页
                {
                    path: '/NursingHome/NursingHome_list',
                    component: resolve => require(['@/page/NursingHome/NursingHome_list.vue'], resolve)
                },
                //养老院管理添加
                {
                    path: '/NursingHome/NursingHome_add',
                    component: resolve => require(['@/page/NursingHome/NursingHome_add.vue'], resolve)
                },
                // 系统设置  
                {
                    path: '/set_up/add_menu',
                    component: resolve => require(['@/page/set_up/add_menu.vue'], resolve)
                },
                {
                    path: '/set_up/menu_list',
                    component: resolve => require(['@/page/set_up/menu_list.vue'], resolve)
                },
                {
                    path: '/set_up/add_role',
                    component: resolve => require(['@/page/set_up/add_role.vue'], resolve)
                },
                {
                    path: '/set_up/role_list',
                    component: resolve => require(['@/page/set_up/role_list.vue'], resolve)
                },
                {
                    path: '/set_up/user_list',
                    component: resolve => require(['@/page/set_up/user_list.vue'], resolve)
                },
                {
                    path: '/set_up/addStaff',
                    component: resolve => require(['@/page/set_up/addStaff.vue'], resolve)
                },
               
                {
                    path: '/index/zhaohuzhe_list',
                    component: resolve => require(['@/page/index/zhaohuzhe_list.vue'], resolve)
                }, // 照护者列表
                {
                    path: '/index/zhaohuzhe_detail',
                    component: resolve => require(['@/page/index/zhaohuzhe_detail.vue'], resolve)
                }, // 照护者详情
                {
                    path: '/index/pingce_jilu_list',
                    component: resolve => require(['@/page/index/pingce_jilu_list.vue'], resolve)
                }, // 评测记录列表
                {
                    path: '/index/pingce_jilu_detail',
                    component: resolve => require(['@/page/index/pingce_jilu_detail.vue'], resolve)
                }, // 评测记录详情  
                {
                    path: '/index/edit_jilu',
                    component: resolve => require(['@/page/index/edit_jilu.vue'], resolve)
                }, // 修改记录
                {
                    path: '/index/zhuanjia_setting',
                    component: resolve => require(['@/page/index/zhuanjia_setting.vue'], resolve)
                }, // 专家干预设置 
                {
                    path: '/index/user_chat',
                    component: resolve => require(['@/page/index/user_chat.vue'], resolve)
                }, // 专家互动列表

                {
                    path: '/index/user_chat_detail',
                    component: resolve => require(['@/page/index/user_chat_detail.vue'], resolve)
                }, // 专家互动详情

                {
                    path: '/index/disclaimer_agreement',
                    component: resolve => require(['@/page/index/disclaimer_agreement.vue'], resolve)
                }, // 免责协议

                {
                    path: '/index/privacy_agreement',
                    component: resolve => require(['@/page/index/privacy_agreement.vue'], resolve)
                }, // 隐私协议

                {
                    path: '/zixun/zixun_list',
                    component: resolve => require(['@/page/zixun/zixun_list.vue'], resolve)
                }, // 资讯列表

                {
                    path: '/cepingguanli/add_ceping_type',
                    component: resolve => require(['@/page/cepingguanli/add_ceping_type.vue'], resolve)
                }, // 添加测评分类

                {
                    path: '/cepingguanli/ceping_type_list',
                    component: resolve => require(['@/page/cepingguanli/ceping_type_list.vue'], resolve)
                }, // 测评分类列表

                {
                    path: '/cepingguanli/ceping_question_list',
                    component: resolve => require(['@/page/cepingguanli/ceping_question_list.vue'], resolve)
                }, // 测评问题列表
                {
                    path: '/cepingguanli/add_ceping_question',
                    component: resolve => require(['@/page/cepingguanli/add_ceping_question.vue'], resolve)
                }, // 添加测评问题
                {
                    path: '/cepingguanli/result_list',
                    component: resolve => require(['@/page/cepingguanli/result_list.vue'], resolve)
                }, // 数据分析列表 
                {
                    path: '/zixun/add_zixun',
                    component: resolve => require(['@/page/zixun/add_zixun.vue'], resolve)
                }, //添加资讯

                //首页- 首页banner
                {
                    path: '/banner/swiper',
                    component: resolve => require(['@/page/banner/swiper.vue'], resolve)
                },
                //首页-添加banner
                {
                    path: '/banner/add_swiper',
                    component: resolve => require(['@/page/banner/add_swiper.vue'], resolve)
                },
                //首页-资讯管理
                {
                    path: '/banner/consulting',
                    component: resolve => require(['@/page/banner/consulting.vue'], resolve)
                },
                //首页-新增文章
                {
                    path: '/banner/add_article',
                    component: resolve => require(['@/page/banner/add_article.vue'], resolve)
                },
                // 订单-工单管理
                {
                    path: '/nav3/order',
                    component: resolve => require(['@/page/nav3/order.vue'], resolve)
                },
                //订单-工单管理-工单详情
                {
                    path: '/nav3/order_detail',
                    component: resolve => require(['@/page/nav3/order_detail.vue'], resolve)
                },
                //助具-类目管理
                {
                    path: '/aid/category_manage',
                    component: resolve => require(['@/page/aid/category_manage.vue'], resolve)
                },
                //助具-类目管理-新增类目
                {
                    path: '/aid/add_category',
                    component: resolve => require(['@/page/aid/add_category.vue'], resolve)
                },
                //助具-助具管理
                {
                    path: '/aid/utilman_manage',
                    component: resolve => require(['@/page/aid/utilman_manage.vue'], resolve)
                },
                //助具-助具管理-新增助具
                {
                    path: '/aid/add_utilman',
                    component: resolve => require(['@/page/aid/add_utilman.vue'], resolve)
                },
                //基础-标签管理
                {
                    path: '/base/sign_manage',
                    component: resolve => require(['@/page/base/sign_manage.vue'], resolve)
                },
                //基础-标签管理-新增标签
                {
                    path: '/base/add_sign',
                    component: resolve => require(['@/page/base/add_sign.vue'], resolve)
                },
                //基础-区域管理
                {
                    path: '/base/area_manage',
                    component: resolve => require(['@/page/base/area_manage.vue'], resolve)
                },
                //基础-区域管理-添加区域
                {
                    path: '/base/add_area',
                    component: resolve => require(['@/page/base/add_area.vue'], resolve)
                },
                //基础-能力问题
                {
                    path: '/base/power_problem',
                    component: resolve => require(['@/page/base/power_problem.vue'], resolve)
                },
                //基础-能力问题-新增问题
                {
                    path: '/base/add_problem',
                    component: resolve => require(['@/page/base/add_problem.vue'], resolve)
                },
                //基础-空间问题
                {
                    path: '/base/space_problem',
                    component: resolve => require(['@/page/base/space_problem.vue'], resolve)
                },
                //基础-空间问题-新增问题
                {
                    path: '/base/add_space_problem',
                    component: resolve => require(['@/page/base/add_space_problem.vue'], resolve)
                },
                //基础-应对方案管理
                {
                    path: '/base/solution_manage',
                    component: resolve => require(['@/page/base/solution_manage.vue'], resolve)
                },
                //基础-应对方案管理-新增应对方案
                {
                    path: '/base/add_solution',
                    component: resolve => require(['@/page/base/add_solution.vue'], resolve)
                },
                //用户-用户管理
                {
                    path: '/user/user_manage',
                    component: resolve => require(['@/page/user/user_manage.vue'], resolve)
                },
                //用户-用户管理-编辑
                {
                    path: '/user/edit_user',
                    component: resolve => require(['@/page/user/edit_user.vue'], resolve)
                },

                //商品列表nav1      
                {
                    path: '/nav1/add_goods_type',
                    component: resolve => require(['@/page/nav1/add_goods_type.vue'], resolve)
                },
                {
                    path: '/nav1/goods_type',
                    component: resolve => require(['@/page/nav1/goods_type.vue'], resolve)
                },
                {
                    path: '/nav1/add_goods_classify',
                    component: resolve => require(['@/page/nav1/add_goods_classify.vue'], resolve)
                },
                {
                    path: '/nav1/goods_classify',
                    component: resolve => require(['@/page/nav1/goods_classify.vue'], resolve)
                },
                {
                    path: '/nav1/comment_detail',
                    component: resolve => require(['@/page/nav1/comment_detail.vue'], resolve)
                },
                {
                    path: '/nav1/comment_list',
                    component: resolve => require(['@/page/nav1/comment_list.vue'], resolve)
                },
                {
                    path: '/nav1/shop_list',
                    component: resolve => require(['@/page/nav1/shop_list.vue'], resolve)
                },
                {
                    path: '/nav1/shop_grouping',
                    component: resolve => require(['@/page/nav1/shop_grouping.vue'], resolve)
                },
                {
                    path: '/nav1/add_shop',
                    component: resolve => require(['@/page/nav1/add_shop.vue'], resolve)
                },
                {
                    path: '/nav1/add_grouping',
                    component: resolve => require(['@/page/nav1/add_grouping.vue'], resolve)
                },
                {
                    path: '/nav1/edit',
                    component: resolve => require(['@/page/nav1/edit.vue'], resolve)
                },
                //nav2
                {
                    path: '/nav2/invitation_list',
                    component: resolve => require(['@/page/nav2/invitation_list.vue'], resolve)
                },

                {
                    path: '/nav2/wx_user_list',
                    component: resolve => require(['@/page/nav2/wx_user_list.vue'], resolve)
                },
                // {
                //   path: '/nav2/invitation_set',
                //   component: resolve => require(['@/page/nav2/invitation_set.vue'], resolve)
                // },
                // {
                //   path: '/nav2/put_forward',
                //   component: resolve => require(['@/page/nav2/put_forward.vue'], resolve)
                // },

                {
                    path: '/nav3/batch',
                    component: resolve => require(['@/page/nav3/batch.vue'], resolve)
                },

                {
                    path: '/nav3/return_goods',
                    component: resolve => require(['@/page/nav3/return_goods.vue'], resolve)
                },
                {
                    path: '/nav3/order_returned_detail',
                    component: resolve => require(['@/page/nav3/order_returned_detail.vue'], resolve)
                },
                // 个人中心设置
                {
                    path: '/setting/staff',
                    component: resolve => require(['@/page/setting/staff.vue'], resolve)
                },
                {
                    path: '/setting/add_staff',
                    component: resolve => require(['@/page/setting/add_staff.vue'], resolve)
                },
                {
                    path: '/setting/order',
                    component: resolve => require(['@/page/setting/order.vue'], resolve)
                },
                {
                    path: '/setting/questions',
                    component: resolve => require(['@/page/setting/questions.vue'], resolve)
                },
                {
                    path: '/setting/add_questions',
                    component: resolve => require(['@/page/setting/add_questions.vue'], resolve)
                },
                {
                    path: '/setting/about_us',
                    component: resolve => require(['@/page/setting/about_us.vue'], resolve)
                },

                // 分销
                {
                    path: '/distribution/set_distribution',
                    component: resolve => require(['@/page/distribution/set_distribution.vue'], resolve)
                }, // 分销设置
                {
                    path: '/distribution/distribution_list',
                    component: resolve => require(['@/page/distribution/distribution_list.vue'], resolve)
                }, // 分销员列表
                {
                    path: '/distribution/distribution_detail',
                    component: resolve => require(['@/page/distribution/distribution_detail.vue'], resolve)
                }, // 分销员列表 - 详情
                {
                    path: '/distribution/income_record',
                    component: resolve => require(['@/page/distribution/income_record.vue'], resolve)
                }, // 收益记录
                {
                    path: '/distribution/cash_withdrawal',
                    component: resolve => require(['@/page/distribution/cash_withdrawal.vue'], resolve)
                }, // 提现管理
                {
                    path: '/distribution/income_details',
                    component: resolve => require(['@/page/distribution/income_details.vue'], resolve)
                }, // 收益记录 - 明细


                // 优惠券
                {
                    path: '/coupon/new_people',
                    component: resolve => require(['@/page/coupon/new_people.vue'], resolve)
                }, // 新人好礼
                {
                    path: '/coupon/add_people',
                    component: resolve => require(['@/page/coupon/add_people.vue'], resolve)
                }, // 添加 - 新人好礼
                {
                    path: '/coupon/coupon_list',
                    component: resolve => require(['@/page/coupon/coupon_list.vue'], resolve)
                }, // 优惠券管理
                {
                    path: '/coupon/coupon_detail',
                    component: resolve => require(['@/page/coupon/coupon_detail.vue'], resolve)
                }, // 优惠券管理 - 优惠券详情
                {
                    path: '/coupon/receive_coupon',
                    component: resolve => require(['@/page/coupon/receive_coupon.vue'], resolve)
                }, // 领取优惠券列表 
                {
                    path: '/coupon/add_coupon',
                    component: resolve => require(['@/page/coupon/add_coupon.vue'], resolve)
                }, // 优惠券管理 - 添加优惠券 

            ]
        },
    ],
})