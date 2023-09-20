let game = document.querySelector(".game");
let ctx2 = game.getContext("2d");

let maze = document.querySelector(".maze");
let ctx = maze.getContext("2d");

document.addEventListener('keydown', keyDown);

let generationComplete = false;

let current;
let goal;

var timesdown = 0;
let seconds = 00;
let tens = 00;
let Interval;
let OutputSeconds = document.getElementById("second");
let OutputTens = document.getElementById("tens");
document.addEventListener('keypress', function(event) {
  if (event.code == 'Space' && 'LeftArrow') {
    if (timesdown == 0) {
      console.log("started");
      startTime();
      timesdown = 1;
    }
  }
});

function startTime(){
  tens++;
  if (tens <= 9){
    OutputTens.innerHTML = "0" + tens;
  }
  if (tens > 9){
    OutputTens.innerHTML = tens;
  }
  if (tens > 99){
    seconds++;
    OutputSeconds.innerHTML = "0" + seconds;
    tens = 0;
    OutputTens.innerHTML = "0" + tens;
  }

  if (seconds > 9){
    OutputSeconds.innerHTML = seconds;
  }
}
class Maze {
  constructor(size, rows, columns) {
    this.size = size;
    this.columns = columns;
    this.rows = rows;
    this.grid = [];
    this.stack = [];
  }

  setup() {
    for (let r = 0; r < this.rows; r++) {
      let row = [];
      for (let c = 0; c < this.columns; c++) {
        let cell = new Cell(r, c, this.grid, this.size);
        row.push(cell);
      }
      this.grid.push(row);
    }
    current = this.grid[0][0];
    this.grid[this.rows - 1][this.columns - 1].goal = true;
  }


  draw() {
    maze.width = this.size;
    maze.height = this.size;
    maze.style.background = "black";
    current.visited = true;
    for (let r = 0; r < this.rows; r++) {
      for (let c = 0; c < this.columns; c++) {
        let grid = this.grid;
        grid[r][c].show(this.size, this.rows, this.columns);
      }
    }

    let next = current.checkNeighbours();
    if (next) {
      next.visited = true;
      this.stack.push(current);
      // current.highlight(this.columns);
      current.removeWalls(current, next);
      current = next;


    } else if (this.stack.length > 0) {
      let cell = this.stack.pop();
      current = cell;
      // current.highlight(this.columns);
    }

    if (this.stack.length === 0) {
      generationComplete = true;
      return;
    }
    this.draw();
  }
}

class Cell {
  constructor(rowNum, colNum, parentGrid, parentSize) {
    this.rowNum = rowNum;
    this.colNum = colNum;
    this.visited = false;
    this.walls = {
      topWall: true,
      rightWall: true,
      bottomWall: true,
      leftWall: true,
    };
    this.valid = {
        topWall: false,
        rightWall: false,
        bottomWall: false,
        leftWall: false,
      };
    this.goal = false;
    this.parentGrid = parentGrid;
    this.parentSize = parentSize;
  }

  checkNeighbours() {
    let grid = this.parentGrid;
    let row = this.rowNum;
    let col = this.colNum;
    let neighbours = [];

    let top = row !== 0 ? grid[row - 1][col] : undefined;
    let right = col !== grid.length - 1 ? grid[row][col + 1] : undefined;
    let bottom = row !== grid.length - 1 ? grid[row + 1][col] : undefined;
    let left = col !== 0 ? grid[row][col - 1] : undefined;

    if (top && !top.visited) neighbours.push(top);
    if (right && !right.visited) neighbours.push(right);
    if (bottom && !bottom.visited) neighbours.push(bottom);
    if (left && !left.visited) neighbours.push(left);

    if (neighbours.length !== 0) {
      let random = Math.floor(Math.random() * neighbours.length);
      return neighbours[random];
    } else {
      return undefined;
    }
  }


  drawTopWall(x, y, size, columns, rows) {
    ctx.beginPath();
    ctx.moveTo(x, y);
    ctx.lineTo(x + size / columns, y);
    ctx.stroke();
  }

  drawRightWall(x, y, size, columns, rows) {
    ctx.beginPath();
    ctx.moveTo(x + size / columns, y);
    ctx.lineTo(x + size / columns, y + size / rows);
    ctx.stroke();
  }

  drawBottomWall(x, y, size, columns, rows) {
    ctx.beginPath();
    ctx.moveTo(x, y + size / rows);
    ctx.lineTo(x + size / columns, y + size / rows);
    ctx.stroke();
  }

  drawLeftWall(x, y, size, columns, rows) {
    ctx.beginPath();
    ctx.moveTo(x, y);
    ctx.lineTo(x, y + size / rows);
    ctx.stroke();
  }
  // highlight(columns) {
  //   // Additions and subtractions added so the highlighted cell does cover the walls
  //   let x = (this.colNum * this.parentSize) / columns + 1;
  //   let y = (this.rowNum * this.parentSize) / columns + 1;
  //   ctx.fillStyle = "purple";
  //   ctx.fillRect(
  //     x,
  //     y,
  //     this.parentSize / columns - 3,
  //     this.parentSize / columns - 3
  //   );
  // }

  removeWalls(cell1, cell2) {
    let x = cell1.colNum - cell2.colNum;
    if (x === 1) {
      cell1.walls.leftWall = false;
      cell2.walls.rightWall = false;
      cell1.valid["leftWall"] = true;
      cell2.valid["rightWall"] = true;
    } else if (x === -1) {
      cell1.walls.rightWall = false;
      cell2.walls.leftWall = false;
      cell1.valid["rightWall"] = true;
      cell2.valid["leftWall"] = true;
    }
    let y = cell1.rowNum - cell2.rowNum;
    if (y === 1) {
      cell1.walls.topWall = false;
      cell2.walls.bottomWall = false;
      cell1.valid["topWall"] = true;
      cell2.valid["bottomWall"] = true;
    } else if (y === -1) {
      cell1.walls.bottomWall = false;
      cell2.walls.topWall = false;
      cell1.valid["bottomWall"] = true;
      cell2.valid["topWall"] = true;

    }
  }

  show(size, rows, columns) {
    let x = (this.colNum * size) / columns;
    let y = (this.rowNum * size) / rows;
    ctx.strokeStyle = "#ffffff";
    ctx.fillStyle = "black";
    ctx.lineWidth = 2;
    if (this.walls.topWall) this.drawTopWall(x, y, size, columns, rows);
    if (this.walls.rightWall) this.drawRightWall(x, y, size, columns, rows);
    if (this.walls.bottomWall) this.drawBottomWall(x, y, size, columns, rows);
    if (this.walls.leftWall) this.drawLeftWall(x, y, size, columns, rows);
    if (this.visited) {
      ctx.fillRect(x + 1, y + 1, size / columns - 2, size / rows - 2);
    }
    if (this.goal) {
      ctx.fillStyle = "rgb(83, 247, 43)";
      ctx.fillRect(x + 1, y + 1, size / columns - 2, size / rows - 2);
    }
  }
}

class Square{
    constructor(maze){
        this.x1 = 0; 
        this.y1 = 0;
        this.maze = maze;
    }
}

function isValid(direction){
    if (direction == "up" && newMaze.grid[player.y1/35][player.x1/35].valid['topWall'] == true) {
        return true;
    }
    if (direction == "down" && newMaze.grid[player.y1/35][player.x1/35].valid['bottomWall'] == true) {
        return true;
    }
    if (direction == "right" && newMaze.grid[player.y1/35][player.x1/35].valid['rightWall'] == true) {
        return true;
    }
    if (direction == "left" && newMaze.grid[player.y1/35][player.x1/35].valid['leftWall'] == true) {
        return true;
    }
    return false;
}
function keyDown(event) {
    // upp
    if (timesdown == 0) {
      Interval=setInterval(startTime, 10);
      timesdown = 1;
    }
    if (event.keyCode == 38) {
        if (isValid("up")) {
            player.y1 -= 35;
        }
    }
    //down
    if (event.keyCode == 40) {
        if (isValid("down")){
            player.y1 += 35;
        }
    }
    //left
    if (event.keyCode == 37) {
        if (isValid("left")){
            player.x1 -= 35;
        }
    }
    //right
    if (event.keyCode == 39) {
        if (isValid("right")){
        player.x1 += 35;
        }
    }
    ctx2.clearRect(0, 0, 630, 630);
    check();
    draw();


}
function check(){
  if (player.x1 == 595 && player.y1 == 595){
    console.log("Winner");
    clearInterval(Interval);
    ending();
  }
}
function draw(){
    console.log(player.x1, player.y1);
    ctx2.fillSytle = 'red';
    ctx2.fillRect(player.x1, player.y1, 35, 35);
}
  
function ending(){
  let score = Math.round(100000 / (seconds + 0.01*tens));
  parent.updateDatabaseScore(score);
  parent.updateOniText("You finished my trial in " + OutputSeconds.innerHTML + "s, so your score is " + score + " points!");
}

var newMaze = new Maze(630, 18, 18);
newMaze.setup();
newMaze.draw();
var player = new Square(newMaze);
draw();
