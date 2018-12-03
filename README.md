
## 前言  
> 开发进度、性能、维护、招聘有困难，90%是工程非技术问题导致痛心的浪费。  
> 个人语言描述自己工程各角度解析为离散小目标的方法！  
> 避免空谈，实例代码如下，  
> 代码[https://github.com/sux789/idea](https://github.com/sux789/idea)  
> 风格[http://test_idea.kono.top/reli/index/listfile](http://test_idea.kono.top/reli/index/listfile)  
> 代码可视化[http://test_idea.kono.top/reli/index/listservice](http://test_idea.kono.top/reli/index/listservice)  
> 代码解析出给前端无需最合适文档：  
>[http://test_idea.kono.top/reli/index/listcontroller](http://test_idea.kono.top/reli/index/listcontroller)  
> 如果您有什么建议，请 mailto:xiang.su@qq.com  
## 需求的方法  
> 我了解方法是三个角度:输入，索引，输出。  
> 是我保持第一位置的个人惯性习惯。  
> 我从中得到：输入输出相互校验，需求点不受干扰，同需求方合约，变更管理。索引用来校验输出，是设计的基础，下面还会说索引。  
> 希望需求方能否如下代码那样分层切割为不耦合点进行管理控制。  
## 解耦代码的方法  
### app层次分解  
> 这是从app角度的解耦。  
> 例如实例代码中关注与交友，分别作为独立app单独测试上线，身份验证算法一致即可。  
> 这放在前面，没有花费，复杂度就自然少了一层，性价比最高。  
### app内分解控制  
* 分解  
* > 代码解析出文档：  
* >[http://test_idea.kono.top/reli/index/listservice](http://test_idea.kono.top/reli/index/listservice)  
* > 文档中看到分解控制器，服务，模型及对应调用关系,及源代码，参数，返回参数说明。  
* > 控制器解析还包含测试表单，相当于一个webapp。  
* >[http://test_idea.kono.top/reli/index/listcontroller?item=index/Index/getCategoryFamily](http://test_idea.kono.top/reli/index/listcontroller?item=index/Index/getCategoryFamily)  
* 控制  
* > 对服务配置文件  
* >[http://test_idea.kono.top/reli/index/listfile?file=config/service.php](http://test_idea.kono.top/reli/index/listfile?file=config/service.php)  
* > 配置了缓存：不用每个人手工处理缓存导致没法维护  
* > 减少工作量，代码更清晰  
* > 自动处理参数：自动适配与装配参数，不用手工一行行读取和装配参数。  
* > 也能跟踪服务对应的sql与执行时间。  
* > 下面类是为了跟踪性能，作为系统生命周期通信写的：  
* >[http://test_idea.kono.top/reli/index/listfile?file=system_service/RuntimeVarManager.php](http://test_idea.kono.top/reli/index/listfile?file=system_service/RuntimeVarManager.php)  
* 好处  
* 可以设计  
* > 设计重要性省略，设计好每一层的接口和参数。  
* 可视化  
* > 代码注释，变量名称和数据库结构辅助生成文档。  
* 可管理  
* > 对项目：结构上变成离散的小目标，讨论问题集中在接口  
* > 对人员：管理人基础在任务清晰；人和任务不耦合；进度更清晰。  
* 可维护  
* > 知道接口调用关系，知道修改model或service方法会影响哪些action。  
### 其他分解  
> 没有说队列/异步，本意不是分解代码  
* 统一错误处理  
* > 统一错误配置管理  
* >[http://test_idea.kono.top/reli/index/listfile?file=exception_zh_cn.php](http://test_idea.kono.top/reli/index/listfile?file=exception_zh_cn.php)  
* > 统一函数code_exception：  
* >[http://test_idea.kono.top/reli/index/listcontroller?item=admin/Misc/verifyLogin](http://test_idea.kono.top/reli/index/listcontroller?item=admin/Misc/verifyLogin)  
* 自动处理返回格式：  
* > 控制器不处理前端格式，错误和返回在一个地方控制  
* >[http://test_idea.kono.top/reli/index/listfile?file=JsonResponse.php](http://test_idea.kono.top/reli/index/listfile?file=JsonResponse.php)  
* 统一函数读取枚举  
* > 比如前端列表框，或者后台用户级别之类常量及对应标题。  
* 系统服务  
* > 实例为放在common/system_service目录代码，同业务service分开。  
* > 比如对nosql规划，不必每一个开发人员都精通，公司统一维护一个系统服务。  
### 总结  
> 层次分明公司不多，如果这样做超过80%公司，项目基本不会出现不能上线情况。如果能自动生成文档与控制，估计不到1%。  
> 多人、长期、大型工程，这是我知道能健康持续下去的唯一保障。  
## 索引的角度解耦逻辑和算法  
### 设计时先索引后表  
> 需求在索引上实现，下面例子会新增表和列的。  
* 索引纵向解耦复杂业务逻辑  
* > 例子是审核流程：  
* >[http://test_idea.kono.top/reli/index/listfile?file=service/TopicAudit.php](http://test_idea.kono.top/reli/index/listfile?file=service/TopicAudit.php)  
* >[http://test_idea.kono.top/reli/index/listfile?file=model/TopicFlow.php](http://test_idea.kono.top/reli/index/listfile?file=model/TopicFlow.php)  
* > 例子topic_flow表不同其他表对应实际表单，而对应的是一个过程。比如某个状态会可能会对应某一张表，对其他表来说是纵向的。  
* > 从一个角度纵向分解流程，需要这个索引，需要增加表。  
* > 没有这个角度思考,就没有这个表，只有代码中实现，如同高空走钢丝，很大可能变成瓶颈所在。如果有这个索引，就有实际存在可靠的可视化的支撑。复杂逻辑流程就变成和这索引相关的多个简单事件，一个系统再复杂也没有几个这样流程。  
* > 什么场景会有?只要有流程都存在。比如物流物流有很多表，还涉及公司内外利益结算。比如审核流程还会循环。  
* 索引解耦算法  
* > 有时索引这思路，需要表结构增加一个列。  
* > 例子可用父分类接口，是英文欠缺字段查询子孙分类，才循环中查询：  
* >[http://test_idea.kono.top/reli/index/listservice?item=TopicCategory/listAvailableParent](http://test_idea.kono.top/reli/index/listservice?item=TopicCategory/listAvailableParent)  
* >[http://test_idea.kono.top/reli/index/listservice?item=TopicCategory/getChildrenIds](http://test_idea.kono.top/reli/index/listservice?item=TopicCategory/getChildrenIds)  
### PHP的算法中的索引观念  
> 六度关系算法：  
>[http://test_idea.kono.top/reli/index/listservice?item=SocialFriend/listSixDegreeRelation](http://test_idea.kono.top/reli/index/listservice?item=SocialFriend/listSixDegreeRelation)  
> PHP代码内数据库无关单纯算法，也需要索引角度去解决。  
> 大数据才能体现算法意义，大数据放在数组中，只有用相关操作数组索引函数，否则没有别的方式。  
### 合理使用索引  
> 数据库是最后一次，前面代码分层一层层保护数据库。  
> 一个sql来举例说明：  
>[http://test_idea.kono.top/reli/index/listfile?file=service/SocialFans.php](http://test_idea.kono.top/reli/index/listfile?file=service/SocialFans.php)  
> 1.使用union all来解耦，这是一个纵向角度。  
> 解耦提供了可选择更小范围。  
> 不光解耦join,写入也提供选择，曾经在一个分类树上统计来自多个表的不同数据，简化了大量代码。  
> 2，如果没有解耦join,条件位置可能是内外层问题中最隐蔽的一种，on属于内层。  
> 条件不直接作用于内层，内层会取出所有数据才能得到结果。  
> 3，索引的长度问题和区分度问题：比如大字段而且前部区分度不足。  
> 4，索引深度：  
> 配置�[http://test_idea.kono.top/reli/index/listfile?file=config/partition.php](http://test_idea.kono.top/reli/index/listfile?file=config/partition.php)  
> 关注用了两个相同结构的表并非错误：  
>[http://test_idea.kono.top/reli/index/listservice?item=SocialIdol/follow](http://test_idea.kono.top/reli/index/listservice?item=SocialIdol/follow)  
>[http://test_idea.kono.top/reli/index/listservice?item=SocialFriend/agree](http://test_idea.kono.top/reli/index/listservice?item=SocialFriend/agree)  
> 5，索引排序效果未知;小数据驱动问题mysql默认适合99%场景，php不用考虑。  
## 文档正确姿势  
> 能省20%直接成本。因为文档有时候比代码更难写，还要维护一致。  
> 也可以脚本去做一些简单的评审数据库设计，代码review。  
## 团队内方法  
> 1，曾经代码review实践：只说各自思路而没有后续，因为我不知道其他方法，但团队提高很大。  
> 2，任务解耦：  
> 上面提供索引纵向解耦，来源很low,早期写代码自己调研，做的是实际的业务，面对的是收据不是产品文档，我没有办法，只有将流程结果算在一张表里面。  
> 这样就养成一个习惯，自己想清楚系统流程，带新人让他做crud,crud涉及到系统流程地方调用我的处理。这样早期高效的原因。  
> 团队只有这样思路，分解为离散任务，比如前面代码解析文档可以看出，需求就像卖豆腐前切豆腐一样，变成独立小目标,才有后续的各种管理和控制。  
*XMind: ZEN - Trial Version*  