# gravity.py
from pygame import *
from math import *
from random import *

X=0
Y=1
VX=2
VY=3
C=4

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

def drawScene(screen, ang, shots):
    screen.fill((0,0,0))
    dx, dy = vectToXY(30, ang)
    draw.line(screen, green, (gunx, guny), (gunx + dx, guny+ dy), 5)
    draw.circle(screen, red, (500, 200), 20)
    for shot in shots:
        draw.circle(screen, shot[C],(int(shot[X]), int(shot[Y])),3)
    
    drawText("Power:%4.1f Number:%d" % (power, len(shots)), 30, 30)
    display.flip()

def moveGun(keys, ang, power, gunx, guny):
    if keys[K_LEFT] == 1:
        ang -= 1
    if keys[K_RIGHT] == 1:
        ang += 1
    if keys[K_UP] == 1:
        power += .01
    if keys[K_DOWN] == 1:
        power -= .01
    if keys[K_w] == 1:
        guny -= 1
    if keys[K_a] == 1:
        gunx -= 1
    if keys[K_s] == 1:
        guny += 1
    if keys[K_d] == 1:
        gunx += 1
    
    return ang, power, gunx, guny

def addGrav(shot, x, y):
    global gravx, gravy
    dx = x - shot[X] 
    dy = y - shot[Y] 
    d = (dx**2 + dy**2)**.5
    dx /= d
    dy /= d
    d2 = d**2
    gravx = dx*1000 / d2
    gravy = dy*1000 / d2
    shot[VX] += gravx
    shot[VY] += gravy
    return d
    

def addShot(ang, power):
    shot = [0,0,0,0,randCol()]
    shot[X], shot[Y] = vectToXY(30,ang)
    shot[X] += gunx
    shot[Y] += guny
    shot[VX], shot[VY] = vectToXY(power,ang)
    return shot

def moveShots(shots):
    killlist = []
    for shot in shots:
        #shot[VY] += .005
        shot[X] += shot[VX]
        shot[Y] += shot[VY]
        d = addGrav(shot, 500, 200)
        if d > 2000:
            killlist.append(shot)
    i=0
    for s in killlist:
        shots.remove(s)    

running = True
shots = []
gunAng = 0.0
myClock = time.Clock()
power = 5.0
gunHeat = 0

while running:
    for evnt in event.get():
        if evnt.type == QUIT:
            running = False
    keys = key.get_pressed()
    if keys[K_ESCAPE]:
        break
    
    gunAng, power,gunx,guny = moveGun(keys, gunAng, power, gunx, guny)

    if keys[K_SPACE] and gunHeat <= 0:
        gunHeat = 20
        shots.append(addShot(gunAng, power))
    if keys[K_x] and len(shots)>0:
        del shots[0]   

    gunHeat -= 1
    moveShots(shots)
    drawScene(screen, gunAng, shots)
    myClock.tick(120)
    
quit()
