# pygame21.py
# Keyboard input
from pygame import *
from math import *
from random import *

UP = 273
DOWN = 274
RIGHT = 275
LEFT = 276
SPACE = 32
ESC = 27
X = 0
Y = 1
VX = 2
VY = 3
C = 4
GRAV = 4

init()

font = font.SysFont("Times New Roman",14)
size = width, height = 1024,768
screen = display.set_mode(size,FULLSCREEN)
green = 0, 255, 0
red = 178, 34, 34
white = 255,255,255
gravx=gravy =0
gunx = 100
guny = 500

def randCol():
    return randint(0,255),randint(0,255),randint(0,255)

def drawText(message,x, y):
    text = font.render( message , 1, (255,0,0))    
    textpos = text.get_rect().move(x, y)            
    screen.blit(text, textpos)

def vectToXY(mag, ang):
    rang = radians(ang)
    x = cos(rang) * mag
    y = sin(rang) * mag
    return x,y

def distance(x1,y1,x2,y2):
    return sqrt((x1-x2)**2 + (y1-y2)**2)

def drawScene(screen, ang, shots, planets):
    screen.fill((0,0,0))
    dx, dy = vectToXY(30, ang)
    draw.line(screen, green, (gunx, guny), (gunx + dx, guny+ dy), 5)
    for p in planets:
        draw.circle(screen, red, tuple(map(int,p[:2])), p[4])
    for shot in shots:
        draw.circle(screen, shot[C],(int(shot[X]), int(shot[Y])),3)
    
    drawText("Power:%4.1f Number:%d" % (power, len(shots)), 30, 30)
    display.flip()

def moveGun(keys, ang, power, gunx, guny):
    if keys[LEFT] == 1:
        ang -= 1
    if keys[RIGHT] == 1:
        ang += 1
    if keys[UP] == 1:
        power += .01
    if keys[DOWN] == 1:
        power -= .01
    if keys[ord("w")] == 1:
        guny -= 1
    if keys[ord("a")] == 1:
        gunx -= 1
    if keys[ord("s")] == 1:
        guny += 1
    if keys[ord("d")] == 1:
        gunx += 1
    
    return ang, power, gunx, guny

def addGrav(shot,p):
    global gravx, gravy
    x,y,vx,vy,gr = p
    dx = x - shot[X] 
    dy = y - shot[Y] 
    d = (dx**2 + dy**2)**.5
    dx /= d
    dy /= d
    d2 = d**2
    gravx = dx*gr*50 / d2
    gravy = dy*gr*50 / d2
    shot[VX] += gravx
    shot[VY] += gravy
    return d

def pGrav(p1,p2):
    dx = p2[X] - p1[X] 
    dy = p2[Y] - p1[Y] 
    d = max(p1[GRAV]+p2[GRAV],(dx**2 + dy**2)**.5)

    dx /= d
    dy /= d
    d2 = d**2
    gravx = dx*p1[GRAV] * p2[GRAV] / d2
    gravy = dy*p1[GRAV] * p2[GRAV] / d2
    p1[VX] += gravx/p1[GRAV]
    p1[VY] += gravy/p1[GRAV]
    return d
    

def addShot(ang, power):
    shot = [0,0,0,0,randCol()]
    shot[X], shot[Y] = vectToXY(30,ang)
    shot[X] += gunx
    shot[Y] += guny
    shot[VX], shot[VY] = vectToXY(power,ang)
    return shot

def addPlanet(ang, power):
    plan = [0,0,0,0,5]
    plan[X], plan[Y] = vectToXY(30,ang)
    plan[X] += gunx
    plan[Y] += guny
    plan[VX], plan[VY] = vectToXY(power/5,ang)
    return plan

def moveShots(shots,planets):
    killlist = []
    for shot in shots:
        shot[X] += shot[VX]
        shot[Y] += shot[VY]
        for p in planets:
            d = addGrav(shot, p)
        if d > 2000 and p is planets[0]:
            killlist.append(shot)
    for s in killlist:
        shots.remove(s)    
    killlist = []
    for p in planets:
        for p2 in planets:
            if p2 is not p:
                d=pGrav(p, p2)
                
        p[X] += p[VX]
        p[Y] += p[VY]  


running = True
planets = [[500,300,0,0,20]]
shots = []
gunAng = 0.0
myClock = time.Clock()
power = 3.0
gunHeat = 0
pHeat = 0

while running:
    for evnt in event.get():
        if evnt.type == QUIT:
            running = False
    keys = key.get_pressed()
    if keys[ESC]:
        break
    
    gunAng, power,gunx,guny = moveGun(keys, gunAng, power, gunx, guny)

    if keys[SPACE] and gunHeat <= 0:
        gunHeat = 20
        shots.append(addShot(gunAng, power))
    if keys[ord("x")] and len(shots)>0:
        del shots[0]   
    if keys[ord("z")]and pHeat <= 0:
        pHeat = 100
        planets.append(addPlanet(gunAng, power))

    gunHeat -= 1
    pHeat -= 1
    moveShots(shots,planets)
    drawScene(screen, gunAng, shots,planets)
    myClock.tick(120)
    
quit()
