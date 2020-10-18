
vim中缩进宽度默认为8个空格。我们可以使用以下命令，来修改缩进宽度：

	:set shiftwidth=4

通过以下设置，每次点击Tab键，将增加宽度为8列的Tab缩进。

	:set tabstop=8
	:set softtabstop=8
	:set shiftwidth=8
	:set noexpandtab

使用以下设置，每次点击Tab键，增加的缩进将被转化为4个空格。

	:set tabstop=4     // 设置 Tab 键宽度为 4 个空格。
	:set softtabstop=4
	:set shiftwidth=4
	:set expandtab

其中，expandtab选项，（expand 扩大）用来控制是否将Tab转换为空格。但是这个选项并不会改变已经存在的文本，如果需要应用此设置将所有Tab转换为空格，需要执行以下命令：

	:retab!
