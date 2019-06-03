#include <stdio.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <stdlib.h>
#include <unistd.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <string.h>
#include <signal.h>

#define BACKLOG 5
#define REMOTE_PORT 9100
#define MAX_RECEIVE 14
#define MAX_SEND 50
#define REMOTE_ADDR "172.16.124.67"

char * s_gets(char * pst,int n);

int main(void)
{
	int fd;
	int cfd; // client fd
	struct sockaddr_in serv_addr, client_addr;
	char buf[MAX_RECEIVE];
	char inputFrame[MAX_SEND];


	fd = socket(AF_INET,SOCK_STREAM,0);


    if(fd == -1){
        perror("server socket create error");
    } else {
        printf("server socket fd is %d \n",fd);
    }

    memset(&serv_addr, 0, sizeof(serv_addr));
	serv_addr.sin_family      = AF_INET;
	serv_addr.sin_port        = htons(REMOTE_PORT);
	serv_addr.sin_addr.s_addr = inet_addr(REMOTE_ADDR);

    if( connect( fd,(struct sockaddr*) &serv_addr, sizeof(struct sockaddr)) == -1 ){
        printf("connect failed \n");
    } else {

        printf("connect success \n");
        recv(fd,buf,MAX_RECEIVE,0);  // 将接收数据打入buf，参数分别是句柄，储存处，最大长度，其他信息（设为0即可）。  recv 会阻塞 、但是 send 不会阻塞
        printf("Received:%s \n",buf);
//
        while( s_gets(inputFrame,MAX_SEND) != NULL && inputFrame[0] != '\0' )
        {

            printf("----------entry------------\n");
            send(fd,inputFrame,MAX_SEND,0);
        }

    }

    // 停止 socket server
    if( close(fd) == 0 )
	{
		printf("socket close successful\n");
		exit(0);
	}
	else
	{
		printf("socket close fail\n");
		exit(1);
	}

	return 0;

}


char * s_gets(char * pst,int n)
{
    char * result_val;
    char * find;

    result_val = fgets(pst,n,stdin);
    if( result_val )
    {
        find = strchr(pst,'\n');

        if(find)
        {
            *find = '\0';
        }
        else
        {
            while( getchar() != '\n')
            {
                continue;
            }
        }
    }

    return result_val;
}
