# hospital-manager-system
## 一、增加科室管理功能
### 功能介绍：
> 1、需要实现增加科室功能；

> 2、能够任意拖动科室的位置进行排序显示；

> 3、能够拖动实现科室的层级分配；

> 4、能够对科室信息进行编辑，删除功能；
### 实现步骤：
#### 一、做些准备工作：
##### ①、新建一个名为hospital的数据库，并在.env文件里配置数据库的信息；    
##### ②、通过命令行工具同时创建模型model、控制器controller、和迁移文件migration
    php artisan make:model Department -mcr
##### ③、创建视图文件(如果要使用模板需要先执行以下php artisan make:auth)：
    resources/views/department.blade.php
##### ④、为上面视图文件添加显示路由：
    Route::get('/department', 'DepartmentController@show')->name('department');
##### ⑤、为了方便访问该页面，在导航栏添加一个新导航：

#### 二、核心来了，使用[laravel-nestedset](http://pilishen.com/posts/laravel-nestedset-the-proper-way-to-implement-infinite-dynamic-multi-level-nested-categories)来实现无限级分类：
##### ①、安装nestedset，运行：
> composer require kalnoy/nestedset
##### ②、配置迁移文件：
###### 你可以使用NestedSet类的columns方法来添加有默认名字的字段：
     ...
    use Kalnoy\Nestedset\NestedSet;//这里千万不要忘记了
    
    public function up() {
        Schema::create('departments', function(Blueprint $table) {
          $table->increments('id');
          $table->unsignedInteger('order');
          $table->string('name')->unique();
          NestedSet::columns($table);
          $table->timestamps();
        });
    } 
##### ③、配置Category模型model：
    ....
    use Kalnoy\Nestedset\NodeTrait;//这里就是添加的地方
    
    class Department extends Model
    {
        use NodeTrait;//这里就是添加的地方
        protected $fillable = ['name','order'];
    }
##### ④、配置完成了，现在运行如下代码迁移文件创建数据表：
    php artisan migrate
##### ⑤、填充数据：(！！注意，不要手工填入，因为产生了lef和right太麻烦了)
###### 执行如下命令，创建数据工厂：
    php artisan make:Department --model=Department Department
##### 编辑工厂文件factories/category.php如下:
    $factory->define(App\Department::class, function (Faker $faker) {
        return [
            'name' => $faker->name,
        ];
    });
##### 在命令行输入如下代码，填充数据：
    php artisan tinker
    
    factory('App\Category',3)->create();
#### 三、这里需要引入vue，因为需要用到它的一些插件来循环输出分类列表，但是vue天生就已经是配置好了的，所以直接使用就行了。

##### 但是在使用之前需要安装设置npm的淘宝镜像，执行如下代码：
> npm config set registry https://registry.npm.taobao.org //设置了镜像才能快速的安装

> npm install

> npm run dev //新增或修改了vue代码后必须进行编译才能生效。

##### 1、列出所有跟分类，并将拖动后的树结构保存到数据库：
###### ①、在resources/assets/js/components目录下创建Departments/DepartmentComponent.vue文件，内容如下：
    <template>
        <div class="dd" id="nestable">
            <ol class="dd-list">
                <department-tree v-for="Department in Departments" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
            </ol>
        </div>
    </template>
    
    <script>
        import DepartmentTree from './DepartmentTree.vue'
        export default {
            components: {
                'department-tree': DepartmentTree
            },
            mounted() {
                console.log('Department2.');
                this.getDepartments();
            },
            data() {
                return {
                    Departments:[]
                }
            },
            methods: {
                getDepartments() {
                    axios.get('/department/get').then(res=> {
                        console.log(res);
                        this.Departments = res.data.data;
                    }).catch(error=> {
                        throw error
                    })
                }
            }
        }
    </script>
###### ②、在resources/assets/js/components目录下创建Departments/DepartmentTree.vue文件，内容如下：
    <template>
        <li class="dd-item">
            <button v-if="Department.children.length > 0" data-action="collapse" type="button">Collapse</button>
            <button data-action="expand" type="button" style="display: none;">Expand</button>
            <div class="dd-handle ">{{ Department.name }}</div>
            <!--必须给这里的ol标签添加判断，不然多了这个ol标签，折叠按钮会出现显示不正常的情况-->
            <ol class="dd-list" v-if="Department.children.length > 0">
                <department-tree v-for="Department in Department.children" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
            </ol>
        </li>
    </template>
    
    <script>
        export default {
            props: ['Department'],
            mounted() {
                console.log('Component mounted.')
            }
        }
    </script>
###### ③、在视图文件resources/views/department.blade.php引入上面的两个组件。
> 这里引入了Nestable的概念，下面的style不可少

    @extends('layouts.app')
    
    @section('head')
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/Nestable/2012-10-15/jquery.nestable.min.js"></script>
        <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <style>
            .dd{position:relative;display:block;margin:0;padding:0;max-width:600px;list-style:none;line-height:20px}.dd-list{display:block;position:relative;margin:0;padding:0;list-style:none}.dd-list .dd-list{padding-left:30px}.dd-collapsed .dd-list{display:none}.dd-item,.dd-empty,.dd-placeholder{display:block;position:relative;margin:0;padding:0;min-height:20px;line-height:20px}.dd-handle,.dd2-content{display:block;min-height:38px;margin:5px 0;padding:8px 12px;background:#f8faff;border:1px solid #dae2ea;color:#7c9eb2;text-decoration:none;font-weight:bold;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.dd-handle:hover,.dd2-content:hover{color:#438eb9;background:#f4f6f7;border-color:#dce2e8}.dd-handle[class*="btn-"],.dd2-content[class*="btn-"]{color:#FFF;border:0;padding:9px 12px}.dd-handle[class*="btn-"]:hover,.dd2-content[class*="btn-"]:hover{opacity:.85;color:#FFF}.dd2-handle+.dd2-content,.dd2-handle+.dd2-content[class*="btn-"]{padding-left:44px}.dd-handle[class*="btn-"]:hover,.dd2-content[class*="btn-"] .dd2-handle[class*="btn-"]:hover+.dd2-content[class*="btn-"]{color:#FFF}.dd-item>button:hover ~ .dd-handle,.dd-item>button:hover ~ .dd2-content{color:#438eb9;background:#f4f6f7;border-color:#dce2e8}.dd-item>button:hover ~ .dd-handle[class*="btn-"],.dd-item>button:hover ~ .dd2-content[class*="btn-"]{opacity:.85;color:#FFF}.dd2-handle:hover ~ .dd2-content{color:#438eb9;background:#f4f6f7;border-color:#dce2e8}.dd2-handle:hover ~ .dd2-content[class*="btn-"]{opacity:.85;color:#FFF}.dd2-item.dd-item>button{margin-left:34px}.dd-item>button{display:block;position:relative;z-index:1;cursor:pointer;float:left;width:25px;height:20px;margin:5px 1px 5px 5px;padding:0;text-indent:100%;white-space:nowrap;overflow:hidden;border:0;background:transparent;font-size:12px;line-height:1;text-align:center;font-weight:bold;top:4px;left:1px;color:#707070}.dd-item>button:before{font-family:FontAwesome;content:'\f067';display:block;position:absolute;width:100%;text-align:center;text-indent:0;font-weight:normal;font-size:14px}.dd-item>button[data-action="collapse"]:before{content:'\f068'}.dd-item>button:hover{color:#707070}.dd-item.dd-colored>button,.dd-item.dd-colored>button:hover{color:#EEE}.dd-placeholder,.dd-empty{margin:5px 0;padding:0;min-height:30px;background:#f0f9ff;border:2px dashed #bed2db;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.dd-empty{border-color:#AAA;border-style:solid;background-color:#e5e5e5}.dd-dragel{position:absolute;pointer-events:none;z-index:999;opacity:.8}.dd-dragel>li>.dd-handle{color:#4b92be;background:#f1f5fa;border-color:#d6e1ea;border-left:2px solid #777;position:relative}.dd-dragel>li>.dd-handle[class*="btn-"]{color:#FFF}.dd-dragel>.dd-item>.dd-handle{margin-top:0}.dd-list>li[class*="item-"]{border-width:0;padding:0}.dd-list>li[class*="item-"]>.dd-handle{border-left:2px solid;border-left-color:inherit}.dd-list>li>.dd-handle .sticker{position:absolute;right:0;top:0}.dd2-handle,.dd-dragel>li>.dd2-handle{left:0;top:0;width:36px;margin:0;border-width:1px 1px 0 0;text-align:center;padding:0!important;line-height:38px;height:38px;background:#ebedf2;border:1px solid #dee4ea;cursor:pointer;overflow:hidden;position:absolute;z-index:1}.dd2-handle:hover,.dd-dragel>li>.dd2-handle{background:#e3e8ed}.dd2-content[class*="btn-"]{text-shadow:none!important}.dd2-handle[class*="btn-"]{text-shadow:none!important;background:rgba(0,0,0,0.1)!important;border-right:1px solid #EEE}.dd2-handle[class*="btn-"]:hover{background:rgba(0,0,0,0.08)!important}.dd-dragel .dd2-handle[class*="btn-"]{border-color:transparent;border-right-color:#EEE}.dd2-handle.btn-yellow{text-shadow:none!important;background:rgba(0,0,0,0.05)!important;border-right:1px solid #FFF}.dd2-handle.btn-yellow:hover{background:rgba(0,0,0,0.08)!important}.dd-dragel .dd2-handle.btn-yellow{border-color:transparent;border-right-color:#FFF}.dd-item>.dd2-handle .drag-icon{display:none}.dd-dragel>.dd-item>.dd2-handle .drag-icon{display:inline}.dd-dragel>.dd-item>.dd2-handle .normal-icon{display:none}.dropzone{border-radius:0;border:1px solid rgba(0,0,0,0.06)}
        </style>
    @endsection
    
    @section('content')
    <div class="container">
        <div id="xx">123</div>
        <department-component></department-component>
    </div>
    @endsection
    
    @section('js')
        <script type="text/javascript">
            jQuery(function($){
                $('.dd').nestable();
                $('#nestable').nestable().on('change', function(){
                    var r = $('.dd').nestable('serialize');
                    //将变换排序后的数据传递给后台的controller进行处理，并保存进入数据库。
                    $.post('/department/change',{'_token':'{{csrf_token()}}','tree':r},function(res){
                        console.log(res)
                    });
                    $("#xx").html(JSON.stringify(r));    //改变排序之后的数据
                });
            });
        </script>
    @endsection
###### ④、修改相应的路由文件web.php:
    //用于显示相应的页面
    Route::get('/department', 'DepartmentController@show')->name('department');
    //用于从数据库获取相关的分类列表
    Route::get('/department/get', 'DepartmentController@get')->name('department.get');
    //用于保存修改后的分类到数据库
    Route::post('/department/change', 'DepartmentController@change')->name('department.change');
###### ⑤、相关控制器DepartmentController内容如下：
    <?php
    
    namespace App\Http\Controllers;
    
    use App\Department;
    use Illuminate\Http\Request;
    
    class DepartmentController extends Controller
    {
        //显示界面
        public function show()
        {
            return view('department');
        }
    
        //将分类以树结构显示出来
        public function get()
        {
            $departments = Department::orderBy('order','ASC')->get()->toTree();
    //        Department::rebuildTree($tree,$delete);  //这为什么不起作用呢，没办法啊。
            return response()->json(['data'=>$departments]);
        }
    
        //将修改后的分类信息保存到数据库
        public function change(Request $request)
        {
            $newTree = $request->get('tree');
    //        $arr = $this->getJsonData($newTree);
            $arrs = $this->getJsonData($newTree);
            foreach ($arrs as $arr) {
                $d = Department::find($arr['id']);
                $d->parent_id = $arr['parent_id'];
                $d->order = $arr['order'];
                $d->save();
            }
            Department::fixTree();
            return response()->json(['data'=>$newTree]);
        }
    
        //将树结构打散，将每个节点的id与parent_id与order组成组成一个数组并放到一个list数组里面。
        private function getJsonData($tree) {
    
    //        $output = json_decode($jsonData,true);
            $list = array();
            $i = 0;
            foreach ($tree as $row) {
                $i++;
                $list[] = array(
                    'id' => $row['id'],
                    'parent_id' => null,
                    'order' => $i
                );
                if (isset($row['children'])) {
                    $this->getChildren($list, $row['id'], $row['children']);
                }
            }
            return $list;
        }
    
        private function getChildren(&$list, $id, $child) {
            $j = 0;
            foreach ($child as $c) {
                $j++;
                $list[] = array(
                    'id' => $c['id'],
                    'parent_id' => $id,
                    'order' => $j
                );
                if (isset($c['children']) ) {
                    $this->getChildren($list, $c['id'], $c['children']);
                }
            }
            return ;
        }
    }
##### 2、对DepartmentComponent.vue文件进行修改，重新排版：
###### 功能实现要求如下：
> 实现增加科室功能：

> 实现展开或收拢科室列表,实现修改完成后统一保存功能：

> 实现编辑、删除某个科室的功能：
###### ①、如果需要实现展开或收拢科室列表需要做一下修改：
###### #首先，修改DepartmentTree.vue文件：
    <template>
        <li class="dd-item">
            <!--需要取消原来这个位置添加的两个按钮-->
            <div class="dd-handle ">{{ Department.name }}</div>
            <!--必须给这里的ol标签添加判断，不然多了这个ol标签，折叠按钮会出现显示不正常的情况-->
            <ol class="dd-list" v-if="Department.children.length > 0">
                <department-tree v-for="Department in Department.children" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
            </ol>
        </li>
    </template>
    
    <script>
        //引入nestable.js
        import nestable from './nestable'
        export default {
            props: ['Department'],
            data() {
                return {
    
                }
            },
            mounted() {
                console.log('Component mounted.');
                //注意，这里需要申明一开始列表的状态时全部展开状态。
                this.expandAll()
    
            },
            methods: {
                //增加一个扩展所有列表的方法
                expandAll() {
                    $('.dd').nestable('expandAll');
                },
            }
        }
    </script>
###### #其次、修改DepartmentComponent.vue文件：
    <template>
        <div class="row">
            <!--显示科室列表-->
            <div class="col-sm-7">
                <div class="card">
                    <div class="card-header">
                        科室列表
                        <div class="btn-group btn-group-sm pull-right">
                        <!--增加相应的按钮及方法-->
                            <button type="button" class="btn btn-success" @click="expandAll">展开所有</button>
                            <button type="button" class="btn btn-success" @click="collapseAll">折叠所有</button>&nbsp;
                            <button type="button" class="btn btn-primary">保存修改</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dd" id="nestable">
                            <ol class="dd-list">
                                <department-tree v-for="Department in Departments" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!--增加科室表单-->
            <div class="col-sm-5">
                <div class="card">
                    <div class="card-header">新增科室</div>
                    <div class="card-body">
                        <input type="text" class="form-control" placeholder="">
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-block btn-success">增加科室</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <script>
        import DepartmentTree from './DepartmentTree.vue'
        //同样需要引入nestable
        import nestable from './nestable'
        export default {
            components: {
                'department-tree': DepartmentTree
            },
            mounted() {
                console.log('Department2.');
                this.getDepartments();
            },
            data() {
                return {
                    Departments:[]
                }
            },
            methods: {
                getDepartments() {
                    axios.get('/department/get').then(res=> {
                        console.log(res);
                        this.Departments = res.data.data;
                    }).catch(error=> {
                        throw error
                    })
                },
                //增加展开的方法
                expandAll() {
                    $('.dd').nestable('expandAll');
                },
                //增加收缩的方法
                collapseAll() {
                    $('.dd').nestable('collapseAll');
                }
    
            }
        }
    </script>
###### ②、如果要实现修改完成后统一保存功能:
###### #首先、需要将department.blade.php里面的js上传代码删除，即不要@section('js')里面的代码了
    <script type="text/javascript">
        jQuery(function($){
            $('.dd').nestable();
            $('#nestable').nestable().on('change', function(){
                var r = $('.dd').nestable('serialize');
                //将变换排序后的数据传递给后台的controller进行处理，并保存进入数据库。
                $.post('/department/change',{'_token':'{{csrf_token()}}','tree':r},function(res){
                    console.log(res)
                });
                $("#xx").html(JSON.stringify(r));    //改变排序之后的数据
            });
        });
    </script>
###### #然后、修改DepartmentComponent.vue文件：
    添加保存按钮：
    <button type="button" class="btn btn-primary" @click="saveChange">保存修改</button>
    
    增加保存方法：
    //保存修改后的树结构到数据库
    saveChange() {
        const r = $('.dd').nestable('serialize');
        axios.post('/department/change',{'tree':r}).then(function (res) {
            console.log('ok')
        })
    }
###### #最后、在DepartmentTree.vue里面的mounted()方法里面添加：
    mounted() {
        console.log('Component mounted.');
        //将初始化前移到此处
        $('.dd').nestable();
        //注意，这里需要申明一开始列表的状态时全部展开状态。
        this.expandAll();
    },
###### ③、实现增加科室功能：
###### 安装并使用vue-treeselect来实现选择上级科室:
> 安装vue-treeselect ，执行：npm install --save @riophae/vue-treeselect

> 在DepartmentComponent.vue文件里面引入vue-treeselect组件：
    
      // import the component
      import Treeselect from '@riophae/vue-treeselect'
      // import the styles
      import '@riophae/vue-treeselect/dist/vue-treeselect.css'
      
      components: {
         'department-tree': DepartmentTree,
         //申明引用组件
         'treeselect':Treeselect
      },
      
      data() {
          return {
              Departments:[],
              //注意，这里必须要用自定义，不然显示不出来的
              normalizer(node) {
                  return {
                      id: node.id,//指定id是什么字段
                      label: node.name,//指定label是用的什么字段，即显示什么字段出来
                  }
              },
          }
      },
> 然后即可在相应的位置使用了：

    <treeselect placeholder="选择上级科室" :normalizer="normalizer" :options="Departments">
> 将数据保存到数据库：
    
    1、在data里面新增两个属性：
    //增加上级科室id属性
    pid:null,
    //增加新增科室名称属性
    departmentName:'',
    
    2、在from表单里面绑定上面的两个属性：
    <form @submit.prevent="addDepartment">
        <div class="card">
            <div class="card-header">新增科室</div>
            <div class="card-body">
                <div class="form-group">
                    <label>选择上级科室:</label>
                    //绑定pid属性
                    <treeselect v-model="pid" placeholder="选择上级科室,不选默认为顶级科室" :normalizer="normalizer" :options="Departments"></treeselect>
                </div>
                <div class="form-group">
                    <label>设置科室名称:</label>
                    //绑定departmentName属性
                    <input v-model="departmentName" type="text" class="form-control">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">增加科室</button>
            </div>
        </div>
    </form>
    
    3、增加保存方法：
    //保存添加的科室到数据库
    addDepartment() {
        axios.post('/department/add',{pid:this.pid,name:this.departmentName}).then((res)=>{
            console.log(res.data.data);
            this.getDepartments();
        })
    }
    
    4、新增保存科室到数据的路由：
    Route::post('/department/add', 'DepartmentController@add')->name('department.add');
    
    5、到控制器里面新增add方法：
    //新增科室保存到数据库
    public function add(Request $request)
    {
        $pid = $request->get('pid');
        $name = $request->get('name');

        $node = new Department(['name'=>$name,'order'=>0]);
        if($pid) {//判断是否选择了上级科室，如果选了上级科，就将它的parent_id设置为上级科室的id
            $node->parent_id = $pid;
            $node->save();
        }
        $node->save();//如果没有选上级科室，默认就保存为跟科室
    }
    
###### ④、实现编辑科室和删除科室功能：
> 首先、添加两个按钮到科室列表里面：
    
    <template>
        <!--注意，这里添加一个dd-nodrag才能让下面添加的按钮生效-->
        <li class="dd-item dd-nodrag">
            <!--需要取消原来这个位置添加的两个按钮-->
            <div class="dd-handle">
    
                {{ Department.name }}
                <!--增加编辑和删除按钮，引入font-awesome-->
                <span class="pull-right">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </span>
            </div>
            <!--必须给这里的ol标签添加判断，不然多了这个ol标签，折叠按钮会出现显示不正常的情况-->
            <ol class="dd-list" v-if="Department.children.length > 0">
                <department-tree v-for="Department in Department.children" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
            </ol>
        </li>
    </template>
> 然后、引入模态框，实现点击编辑按钮弹出编辑科室的模态框，然后进行编辑：
    
    ①、引入模态框DepartmentModel.vue
    
    <template>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container">
    
                        <div class="modal-header">
                            <slot name="header">
                                default header
                              </slot>
                        </div>
    
                        <div class="modal-body">
                            <slot name="body">
                                default body
                              </slot>
                        </div>
    
                        <div class="modal-footer">
                            <slot name="footer">
                                default footer
                                <button class="modal-default-button" @click="$emit('close')">
                                OK
                              </button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </template>
    <!--设置模态框的显示样式-->
    <style media="screen">
        .modal-mask {
            position: fixed;
            z-index: 9998;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, .5);
            display: table;
            transition: opacity .3s ease;
        }
    
        .modal-wrapper {
            display: table-cell;
            vertical-align: middle;
        }
    
        .modal-container {
            width: 300px;
            margin: 0px auto;
            padding: 20px 30px;
            background-color: #fff;
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
            transition: all .3s ease;
            font-family: Helvetica, Arial, sans-serif;
        }
    
        .modal-header h3 {
            margin-top: 0;
            color: #42b983;
        }
    
        .modal-body {
            margin: 20px 0;
        }
    
        .modal-default-button {
            float: right;
        }
    
        /*
         * The following styles are auto-applied to elements with
         * transition="modal" when their visibility is toggled
         * by Vue.js.
         *
         * You can easily play with the modal transition by editing
         * these styles.
         */
    
        .modal-enter {
            opacity: 0;
        }
    
        .modal-leave-active {
            opacity: 0;
        }
    
        .modal-enter .modal-container,
        .modal-leave-active .modal-container {
            -webkit-transform: scale(1.1);
            transform: scale(1.1);
        }
    </style>
    
    <script>
    
    </script>
    
    ②、注册DepartmentModel模态框,到app.js添加如下代码
    Vue.component('department-model', require('./components/Departments/DepartmentModel.vue'));
    
    ③、修改DepartmentTree.vue，实现点击显示模态框,点击模态框取消按钮隐藏模态框
    //这里需要引入Departments，vue-treeselect从才能使用
    
    <!--这里需要添加:Departments="Departments"进来从能让vue-treeselect起作用-->
    <department-tree v-for="Department in Department.children" :key="Department.id" :Departments="Departments" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>

    props: ['Department','Departments'],
    
    data() {
        return {
            //判断编辑科室的模态框是否显示，默认不显示
            showEditDepartment:false,
            //增加上级科室id属性
            pid:null,
            //增加新增科室名称属性
            departmentName:'',
            //注意，这里必须要用自定义，不然显示不出来的
            normalizer(node) {
                return {
                    id: node.id,//指定id是什么字段
                    label: node.name,//指定label是用的什么字段，即显示什么字段出来
                }
            },
        }
    },  
    
    //然后，给添加按钮一个点击事件，让实现变成显示，从而实现点击添加按钮显示出模态框
    <i @click="showEditDepartment=true" class="fa fa-pencil-square-o" aria-hidden="true"></i>
    
    //再然后、引入模态框：
    //首先、引入DepartmentModel
        import DepartmentModel from './DepartmentModel.vue'
        
    //引入vue-treeselect
    import Treeselect from '@riophae/vue-treeselect'
    //引入vue-treeselect的样式
    import '@riophae/vue-treeselect/dist/vue-treeselect.css'
        
    //然后再使用 
    <department-model v-if="showEditDepartment">
        <h3 slot="header">编辑科室</h3>
        <div slot="body">
            <div class="form-group">
                <label>选择上级科室:</label>
                <treeselect v-model="pid" placeholder="不选默认为顶级科室" :normalizer="normalizer" :options="Departments"></treeselect>
            </div>
            <div class="form-group">
                <label>设置科室名称:</label>
                <input v-model="departmentName" type="text" class="form-control">
            </div>
        </div>
        <button @click="editDepartment" class="btn btn-sm btn-success" slot="footer">保存</button>
        <!--实现点击取消按钮，隐藏模态框-->
        <button class="btn btn-sm btn-default" slot="footer" @click="showEditDepartment=false">取消</button>
    </department-model>
> 再然后、编写两个方法editDepartment，delDepartment：
    
    //增加编辑部门的方法
    editDepartment() {
        axios.post('/department/edit',{pid:this.pid,name:this.departmentName,id:this.Department.id}).then((res)=>{
            console.log('修改成功');
            //调用父组件的方法，实现添加新分类后马上显示出来，但是不要忘记到父组件里面添加这个方法@getDepartments="getDepartments"
            //<department-tree v-for="Department in Department.children" @getDepartments="getDepartments" :key="Department.id" :Departments="Departments" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
            this.getDepartments()
        })
    },
    //增加删除部门的方法
    delDepartment() {
        axios.post('/department/delete',{id:this.Department.id}).then((res)=>{
            console.log('删除成功');
            this.getDepartments()
        })
    },
    //必须增加这个方法与父组件的名称一样这很重要，本组件递归调用才不会报错
    getDepartments() {
        this.$emit('getDepartments')
    }
    
> 再再然后、新增两条路由对于上面的两个方法：

    Route::post('/department/edit', 'DepartmentController@edit')->name('department.edit');
    Route::post('/department/delete', 'DepartmentController@delete')->name('department.delete');

> 最后，控制器添加两个对于的方法：
    
    //编辑科室然后保存到数据库
    public function edit(Request $request)
    {
        $pid = $request->get('pid');
        $name = $request->get('name');
        $id = $request->get('id');

        $node = Department::find($id);

        $node->parent_id = $pid;
        $node->name = $name;
        $node->save();
    }
    
    //删除科室
    public function delete(Request $request)
    {
        $id = $request->get('id');
        $node = Department::find($id);
        $node->delete();
    }
###### ⑤、修复细节问题：即增加第一个子列表的时候，父列表不显示折叠按钮，删除最后一个子列表的时候，父列表又不取消折叠按钮的显示：
    //下面时新增的时候：
    addDepartment() {
        axios.post('/department/add',{pid:this.pid,name:this.departmentName}).then((res)=>{
            console.log(res.data.data);
            this.departmentName ='';
            
            
            //注意下面的拼接写法很重要哦！
            const li = $('li[data-id='+this.pid+']');
            //增加分类列表的时候给它的父级列表添加一个折叠按钮
            this.addClass(li);
            
            
            this.getDepartments();
        })
    },
    //同时添加addClass方法，这时参考了nestable.js的源码来实现的
    addClass: function(li)
    {
        if(li.children('ol').length === 0) {
            li.prepend('<button data-action="collapse" type="button">Collapse</button>');
            li.prepend('<button data-action="expand" type="button" style="display: none">Expand</button>');
        }
    },
    
    //下面时删除的时候：
    delDepartment() {
        axios.post('/department/delete',{id:this.Department.id}).then((res)=>{
            console.log('删除成功');
            
            const li = $('li[data-id='+this.pid+']');
            this.delClass(li);
            
            
            this.getDepartments();
        })
    },
    
    delClass: function(li)
    {
        if(li.children('ol').children('li').length === 1) {
            li.children('[data-action="collapse"]').remove();
        }
    },
    
    
