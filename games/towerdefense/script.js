const canvas = document.getElementById("canvas1");
const ctx = canvas.getContext("2d");
const Basic = document.getElementById("basic");
const Rapid = document.getElementById("rapid");
const Heavy = document.getElementById("heavy");
const dragon = document.getElementById("dragon_head");
function returnButtons(){//function that is called in html document when basic button is pressed
	alert("Hello World");
}
//canvas.width = 900;
//canvas.height = 600;
//width was 900
//height was 600

//global variables
const cellSize = 75;
//change cellsize to 75 (original = 100)
const cellGap = 3;
const gameGrid = [];
const defenders = [];
let numberOfRescources = 300;
const enemies = [];
const enemyPositions = [];
let enemiesInterval = 600;
//enemies interval sets the gap between each enemy being produced (decrease = more enemies)
let frame = 0;
let gameOver = false;
const projectiles = [];
let score = 0;
const rescources = [];
const winningScore = 50;
//changing winning score = changes how long the game goes on for (increase = longer)

//mouse
const mouse = {
	x: undefined,
	y: undefined,
	width: 0.1,
	height: 0.1,
};
let canvasPosition = canvas.getBoundingClientRect();
canvas.addEventListener("mousemove",function(e){
	mouse.x = e.x - canvasPosition.left;
	mouse.y = e.y - canvasPosition.top;
});//my code does not fucking work
canvas.addEventListener("mouseleave",function(e){
	mouse.x = undefined;
	mouse.y = undefined;
});

//game board
const controlsBar = {
	width: canvas.width,
	height: cellSize,
};
class Cell{
	constructor(x,y){
		this.x = x;
		this.y = y;
		this.width = cellSize;
		this.height = cellSize;
	}
	draw(){
		if (mouse.x && mouse.y && collision(this, mouse)) {
			ctx.strokeStyle = "white";
			ctx.strokeRect(this.x,this.y,this.width,this.height);
		}
	}
}
function createGrid(){
	for (let y = cellSize; y < canvas.height; y += cellSize) {
		for (let x= 0; x < canvas.width; x += cellSize) {
			gameGrid.push(new Cell(x,y));//adds a new object of the class cell to the array gameGrid
		}
	}
}
createGrid();
function handleGameGrid(){
	for (let i = 0; i < gameGrid.length; i++) {
		gameGrid[i].draw();//this will call the method of each grid class and draw a rectangle
	}
}
//projectiles
class Projectiles{
	constructor(x,y){
		this.x = x;
		this.y = y;
		this.width = 10;
		this.height = 10;
		this.power = 20;
		//changes amount it destroys things by
		this.speed = 5;
		//changes the speed at which the projectile moves across the screen
	}
	update(){
		this.x += this.speed;
	}
	draw(){
		ctx.fillStyle = "orange";
		ctx.beginPath();
		ctx.arc(this.x, this.y, this.width, 0, Math.PI*2);
		ctx.fill();
	}
}
function handleProjectiles(){
	for (let i = 0; i < projectiles.length; i++) {
		projectiles[i].update();
		projectiles[i].draw();
		for (let j = 0; j < enemies.length; j++) {
			if (enemies[j] &&projectiles[i] && collision(projectiles[i],enemies[j])){
				enemies[j].health -= projectiles[i].power;
				projectiles.splice(i,1);
				i--;
			}
		}
		if (projectiles[i] && projectiles[i].x > canvas.width - cellSize) {
			projectiles.splice(i,1);
			i--;
		}
	}
}

//defenders
class Defender{
	constructor(x,y){
		this.x = x;
		this.y = y;
		this.width = cellSize - cellGap * 2;
		this.height = cellSize - cellGap * 2;
		this.shooting = false;
		this.health = 100;
		this.projectiles = [];
		this.timer = 0;
	}
	draw(){
		ctx.fillStyle = "blue";
		ctx.fillRect(this.x,this.y,this.width,this.height);
		ctx.fillStyle = "gold";
		ctx.font = "30px Arial";
		ctx.fillText(Math.floor(this.health),this.x+15,this.y+30);
	}
	update(){
		if (this.shooting){
			this.timer++;
			if (this.timer % 100 == 0){
				//changing this number changes the amount of projectiles
				projectiles.push(new Projectiles(this.x + 35,this.y+35));
				//original offsetting for projectiles (this.x+50,this.y+50)
			}
		}
		else{
			this.timer = 0;
		}
	}
}
canvas.addEventListener("click",function(){
	const gridPositionX = mouse.x - (mouse.x % cellSize) + cellGap;
	const gridPositionY = mouse.y - (mouse.y % cellSize) + cellGap;
	if (gridPositionY < cellSize) return;//just ends function here
	for (let i = 0; i < defenders.length; i++) {
		if((defenders[i].x == gridPositionX) && (defenders[i].y == gridPositionY))return;//just ends function here
	}
	let defenderCost = 100;
	if (numberOfRescources >= defenderCost){
		defenders.push(new Defender(gridPositionX,gridPositionY));
		numberOfRescources -= defenderCost;
	}
});
function handleDefenders(){
	for (let i = 0; i < defenders.length; i++) {
		defenders[i].draw();
		defenders[i].update();
		if (enemyPositions.indexOf(defenders[i].y) !== -1) {
			defenders[i].shooting = true;
		}
		else{
			defenders[i].shooting = false;
		}
		for (let j = 0; j < enemies.length; j++) {
			if(defenders[i] && collision(defenders[i],enemies[j])){
				enemies[j].movement = 0;
				defenders[i].health -= 0.2;
			}
			if(defenders[i] && defenders[i].health <= 0){
				defenders.splice(i, 1);//passes 1 cause it wants to remove one element from this index i
				i--;
				enemies[j].movement = enemies[j].speed;
			}
		}
	}
}

//enemies
class Enemy{
	constructor(verticalPosition){
		this.x = canvas.width;
		this.y = verticalPosition;
		this.width = cellSize - cellGap*2;
		this.height = cellSize - cellGap*2;
		this.speed = Math.random() * 0.2 + 0.4;
		//original speed (math.random*0.2 + 0.4) - changing the first number chnages the randomness in speed - changing the second number changes the speed in general
		this.movement = this.speed;
		this.health = 100;
		this.maxHealth = this.health;
	}
	update(){
		this.x -= this.movement;
	}
	draw(){
		ctx.fillStyle = "red";
		//ctx.fillRect(this.x, this.y, this.width,this.height);
		ctx.drawImage(dragon,this.x, this.y, this.width + 20,this.height + 20);
		ctx.fillStyle = "red";
		ctx.font = "30px Arial";
		ctx.fillText(Math.floor(this.health),this.x+75,this.y+50);
	}
}
function handleEnemies(){
	for (let i = 0; i < enemies.length; i++) {
		enemies[i].update();//moves the enemeis to the left
		enemies[i].draw();
		if(enemies[i].x < 0){
			gameOver = true;
			//console.log(gameOver);
		}
		if (enemies[i].health <= 0){
			let gainedRescources = enemies[i].maxHealth/10;
			numberOfRescources += gainedRescources;
			score += gainedRescources;
			const findThisIndex = enemyPositions.indexOf(enemies[i].y);
			enemyPositions.splice(findThisIndex, 1);
			enemies.splice(i,1);
			i--;
		}
	}
	if (frame % enemiesInterval == 0 && score < winningScore){
		let verticalPosition = Math.floor(Math.random()*5+1)*cellSize + cellGap;
		enemies.push(new Enemy(verticalPosition));
		enemyPositions.push(verticalPosition);
		if (enemiesInterval > 120) {
			enemiesInterval -= 50;
		}
	}
}

//rescources
const amounts = [20,30,40];
class Rescource{
	constructor(){
		this.x = Math.random() * (canvas.width - cellSize);
		this.y = (Math.floor(Math.random()*5)+1)*cellSize + 25;
		// instead of (+25) original was (+25)
		this.width = cellSize * 0.6;
		this.height = cellSize * 0.6;
		this.amount = amounts[Math.floor(Math.random()*amounts.length)];
	}
	draw(){
		ctx.fillStyle = "yellow";
		ctx.fillRect(this.x,this.y,this.width,this.height);
		ctx.fillStyle = "black";
		ctx.font = "20px Arial";
		ctx.fillText(this.amount, this.x+15, this.y+25);
	}
}
function handleRescources(){
	if (frame % 500 == 0 && score < winningScore) {
		//frame is the probability thing as to wether a rescource is drawn
		rescources.push(new Rescource());
	}
	for (let i = 0; i < rescources.length; i++) {
		rescources[i].draw();
		if (rescources[i] && mouse.x && mouse.y && collision(rescources[i], mouse)) {
			numberOfRescources += rescources[i].amount;
			rescources.splice(i,1);
			i--;
		}
	}
}

//utilities
function handleGameStatus(){
	ctx.fillStyle = "gold";
	ctx.font = "20px arial";
	//original font = 30px
	ctx.fillText("Rescources: " + numberOfRescources, 20, 30);
	//original co-ordinates = (20,35)
	ctx.fillText("Score: " + score, 20, 60);
	//original co-ordinates = (20,75)
	/*if (gameOver) {
		ctx.clearRect(0,0,canvas.width,canvas.height)
		ctx.fillStyle = "black";
		ctx.fillRect(0,0,canvas.width,canvas.height);
		ctx.fillStyle = "white";
		ctx.font = "60px arial";
		ctx.fillText = ("GAME OVER", 130, 300);
		console.log(gameOver);
		setTimeout(3000)
		//need to be able to restart when gameOver
	}*/
	if (score >= winningScore && enemies.length == 0){
		ctx.fillStyle = "white";
		ctx.font = "60px arial";
		ctx.fillText("LEVEL COMPLETE",130,300);
		ctx.font = "30px arial";
		ctx.fillText("You win with " +score+ "points",134,340);
		//need to reset game after this as passed level. move on to harder level
		console.log(defenders);
		//must reset all variables
		animate();//crashes here
	}
}

function animate(){
	//doesnt do anything cause its clearRect not fillRect
	ctx.clearRect(0,0,canvas.width,canvas.height);//clears everything on board and does it again, obvious when we comment it out
	ctx.fillStyle = "black";
	ctx.fillRect(0,0,canvas.width,canvas.height);
	//ctx.fillStyle = "blue";
	ctx.fillRect(0,0,controlsBar.width, controlsBar.height);
	handleGameGrid();
	handleDefenders();
	handleRescources();
	handleProjectiles();
	handleEnemies();
	handleGameStatus();
	frame++;
	if(!(gameOver)){
		requestAnimationFrame(animate);//game loop recursion
	}
	/*else{
		ctx.clearRect(0,0,canvas.width,canvas.height)
		ctx.fillStyle = "black";
		ctx.fillRect(0,0,canvas.width,canvas.height);
		ctx.fillStyle = "orange";
		ctx.font = "120px arial";
		ctx.fillText = ("GAME OVER!", 50, 50);
	}*/
}
animate();

function collision(first, second){
	if (!(first.x>second.x+second.width || first.x + first.width < second.x || first.y > second.y + second.height || first.y + first.height < second.y )) {
		return true;
	}
}

window.addEventListener("Resize", function(){
	canvasPosition = canvas.getBoundingClientRect();
})



//make rapid towers that shoot a lot of low power (cost less)
//keep average ones as they are (power = 20, health = 100, cost = 100, speed = average)
//make one that shoots very high power but very few (costs more)
//can sell old towers for half price
