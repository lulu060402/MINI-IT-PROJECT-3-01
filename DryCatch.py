import pygame
import random
import os
import sys

pygame.init()

WIDTH, HEIGHT = 800, 1000
screen = pygame.display.set_mode((WIDTH, HEIGHT))
pygame.display.set_caption("Catch the Laundry - Press SPACE to Boost!")



WHITE = (255, 255, 255)
BLACK = (0, 0, 0)
RED = (255, 0, 0)

score = 0
font = pygame.font.SysFont('Arial', 36)
clock = pygame.time.Clock()

CLOTHES_SIZE = 80
BASKET_WIDTH = 120
BASKET_HEIGHT = 60

basket_img = pygame.image.load('basket.png').convert_alpha()
basket_img = pygame.transform.scale(basket_img, (BASKET_WIDTH, BASKET_HEIGHT))
    
clothes_imgs = {
        'shirt': pygame.image.load('mmushirt.png').convert_alpha(),
        'pants': pygame.image.load('pants.png').convert_alpha(),
        'sock': pygame.image.load('socks.png').convert_alpha(),
        'skirt': pygame.image.load('skirt.png').convert_alpha(),
        'underwear': pygame.image.load('underwear.png').convert_alpha(),
    }
for key in clothes_imgs:
        clothes_imgs[key] = pygame.transform.scale(clothes_imgs[key], (CLOTHES_SIZE, CLOTHES_SIZE))
        
basket_x = WIDTH // 2 - BASKET_WIDTH // 2
basket_y = HEIGHT - BASKET_HEIGHT - 40
normal_speed = 10
boost_speed = 50
current_speed = normal_speed

clothes = []
clothes_speed = 5
spawn_rate = 30




running = True
while running:
    screen.fill(BLACK)
    

    for event in pygame.event.get():
        if event.type == pygame.QUIT:
            running = False
    
    keys = pygame.key.get_pressed()
    current_speed = boost_speed if keys[pygame.K_SPACE] else normal_speed
    
    if keys[pygame.K_a] and basket_x > 0:
        basket_x -= current_speed
    if keys[pygame.K_d] and basket_x < WIDTH - BASKET_WIDTH:
        basket_x += current_speed
    
    if random.randint(1, spawn_rate) == 1:
        clothes.append({
            'x': random.randint(0, WIDTH - CLOTHES_SIZE),
            'y': -CLOTHES_SIZE,
            'type': random.choice(['shirt', 'pants', 'sock', 'skirt', 'underwear'])
        })
    
    for item in clothes[:]:
        item['y'] += clothes_speed
        
        screen.blit(clothes_imgs[item['type']], (item['x'], item['y']))
        
        if (basket_x < item['x'] + CLOTHES_SIZE and
            basket_x + BASKET_WIDTH > item['x'] and
            basket_y < item['y'] + CLOTHES_SIZE and
            basket_y + BASKET_HEIGHT > item['y']):
            clothes.remove(item)
            score += 1
        
        elif item['y'] > HEIGHT:
            clothes.remove(item)
            if score > 0:
                score -= 1
    
    screen.blit(basket_img, (basket_x, basket_y))
    
    score_text = font.render(f"Score: {score}", True, WHITE)
    screen.blit(score_text, (20, 20))
    
    if keys[pygame.K_SPACE]:
        boost_text = font.render("BOOST!", True, RED)
        screen.blit(boost_text, (WIDTH - 120, 20))
    
    pygame.display.flip()
    clock.tick(60)

pygame.quit()
sys.exit()