<html>
    <head></head>
    <body>
        <div>
            <h1>系统提示</h1>
            {if $_code !=0}
               <b> 错误代码:</b>{$_code}
                <br />
            {/if}
            <b>出错信息:</b> {$_msg}<br />
            {if !empty($_exception_detail)}
                <div>
                 <b>详细信息: </b>Exception  in {$_exception_detail.file}  on {$_exception_detail.line} <br/>
                 <dl>
                     <dt><b>错误跟踪:</b> </dt>
                     <dd>{$_exception_detail.trace}</dd>
                 <dl>
                </div>
            {/if}
        </div>

    </body>
</html>