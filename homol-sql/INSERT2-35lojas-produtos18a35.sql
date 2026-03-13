-- =====================================================
-- LOJA 18: Taco El Mexican (20 produtos - mexicana)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PIZZAS SALGADAS TRADICIONAIS (subcategoria 27) - 15 produtos
(18, 27, 'personalizavel', 'Margherita (6 fatias) 🍕', 'Molho de tomate, mussarela, manjericão', 'margherita-6', 39.90, 34.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, manjericão', 20, 1, 1, 1),
(18, 27, 'personalizavel', 'Margherita (8 fatias) 🍕', 'Molho de tomate, mussarela, manjericão', 'margherita-8', 49.90, 44.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, manjericão', 25, 1, 1, 2),
(18, 27, 'personalizavel', 'Margherita (12 fatias) 🍕', 'Molho de tomate, mussarela, manjericão', 'margherita-12', 69.90, 59.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, manjericão', 30, 1, 0, 3),
(18, 27, 'personalizavel', 'Mussarela (6 fatias) 🧀', 'Molho de tomate, mussarela, orégano', 'mussarela-6', 37.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, orégano', 20, 1, 1, 4),
(18, 27, 'personalizavel', 'Mussarela (8 fatias) 🧀', 'Molho de tomate, mussarela, orégano', 'mussarela-8', 47.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, orégano', 25, 1, 0, 5),
(18, 27, 'personalizavel', 'Mussarela (12 fatias) 🧀', 'Molho de tomate, mussarela, orégano', 'mussarela-12', 67.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, orégano', 30, 1, 0, 6),
(18, 27, 'personalizavel', 'Calabresa (6 fatias) 🌶️', 'Molho, mussarela, calabresa, cebola', 'calabresa-6', 42.90, 37.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, calabresa, cebola', 20, 1, 1, 7),
(18, 27, 'personalizavel', 'Calabresa (8 fatias) 🌶️', 'Molho, mussarela, calabresa, cebola', 'calabresa-8', 52.90, 47.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, calabresa, cebola', 25, 1, 1, 8),
(18, 27, 'personalizavel', 'Calabresa (12 fatias) 🌶️', 'Molho, mussarela, calabresa, cebola', 'calabresa-12', 72.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho de tomate, mussarela, calabresa, cebola', 30, 1, 0, 9),
(18, 27, 'personalizavel', 'Portuguesa (6 fatias) 🇵🇹', 'Molho, mussarela, presunto, ovo, cebola, azeitona', 'portuguesa-6', 45.90, 40.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, presunto, ovo, cebola, azeitona', 22, 1, 1, 10),
(18, 27, 'personalizavel', 'Portuguesa (8 fatias) 🇵🇹', 'Molho, mussarela, presunto, ovo, cebola, azeitona', 'portuguesa-8', 55.90, 50.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, presunto, ovo, cebola, azeitona', 27, 1, 1, 11),
(18, 27, 'personalizavel', 'Portuguesa (12 fatias) 🇵🇹', 'Molho, mussarela, presunto, ovo, cebola, azeitona', 'portuguesa-12', 75.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, presunto, ovo, cebola, azeitona', 32, 1, 0, 12),
(18, 27, 'personalizavel', 'Frango com Catupiry (6 fatias) 🐔', 'Molho, mussarela, frango, catupiry', 'frango-catupiry-6', 44.90, 39.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, frango, catupiry', 22, 1, 1, 13),
(18, 27, 'personalizavel', 'Frango com Catupiry (8 fatias) 🐔', 'Molho, mussarela, frango, catupiry', 'frango-catupiry-8', 54.90, 49.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, frango, catupiry', 27, 1, 1, 14),
(18, 27, 'personalizavel', 'Frango com Catupiry (12 fatias) 🐔', 'Molho, mussarela, frango, catupiry', 'frango-catupiry-12', 74.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, frango, catupiry', 32, 1, 0, 15),

-- PIZZAS ESPECIAIS (subcategoria 27) - 8 produtos
(18, 27, 'personalizavel', 'Quatro Queijos (6 fatias) 🧀🧀', 'Molho, mussarela, provolone, parmesão, gorgonzola', 'quatro-queijos-6', 48.90, 43.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, provolone, parmesão, gorgonzola', 22, 1, 1, 16),
(18, 27, 'personalizavel', 'Quatro Queijos (8 fatias) 🧀🧀', 'Molho, mussarela, provolone, parmesão, gorgonzola', 'quatro-queijos-8', 58.90, 53.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, provolone, parmesão, gorgonzola', 27, 1, 1, 17),
(18, 27, 'personalizavel', 'Pepperoni (6 fatias) 🍕', 'Molho, mussarela, pepperoni', 'pepperoni-6', 46.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, pepperoni', 20, 1, 1, 18),
(18, 27, 'personalizavel', 'Pepperoni (8 fatias) 🍕', 'Molho, mussarela, pepperoni', 'pepperoni-8', 56.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, pepperoni', 25, 1, 0, 19),
(18, 27, 'personalizavel', 'Margherita com Rúcula (6 fatias) 🍃', 'Molho, mussarela, tomate seco, rúcula', 'margherita-rucula-6', 49.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, tomate seco, rúcula', 22, 1, 1, 20),
(18, 27, 'personalizavel', 'Margherita com Rúcula (8 fatias) 🍃', 'Molho, mussarela, tomate seco, rúcula', 'margherita-rucula-8', 59.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, tomate seco, rúcula', 27, 1, 0, 21),
(18, 27, 'personalizavel', 'Parma com Rúcula (6 fatias) 🍖', 'Molho, mussarela, parma, rúcula, parmesão', 'parma-rucula-6', 56.90, 51.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, presunto parma, rúcula, parmesão', 22, 1, 1, 22),
(18, 27, 'personalizavel', 'Parma com Rúcula (8 fatias) 🍖', 'Molho, mussarela, parma, rúcula, parmesão', 'parma-rucula-8', 66.90, 61.90, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, presunto parma, rúcula, parmesão', 27, 1, 1, 23),

-- PIZZAS DOCES (subcategoria 28) - 5 produtos
(18, 28, 'personalizavel', 'Pizza de Chocolate (6 fatias) 🍫', 'Chocolate ao leite, granulado', 'pizza-chocolate-6', 44.90, 39.90, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300', 'Massa, chocolate ao leite, granulado', 15, 1, 1, 24),
(18, 28, 'personalizavel', 'Pizza de Chocolate (8 fatias) 🍫', 'Chocolate ao leite, granulado', 'pizza-chocolate-8', 54.90, 49.90, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300', 'Massa, chocolate ao leite, granulado', 20, 1, 1, 25),
(18, 28, 'personalizavel', 'Pizza de Banana com Canela (6 fatias) 🍌', 'Banana, canela, leite condensado', 'pizza-banana-6', 46.90, NULL, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300', 'Massa, banana, canela, leite condensado', 15, 1, 1, 26),
(18, 28, 'personalizavel', 'Pizza de Banana com Canela (8 fatias) 🍌', 'Banana, canela, leite condensado', 'pizza-banana-8', 56.90, NULL, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300', 'Massa, banana, canela, leite condensado', 20, 1, 0, 27),
(18, 28, 'personalizavel', 'Pizza de Brigadeiro com Morango (8 fatias) 🍓', 'Brigadeiro, morango', 'pizza-brigadeiro-8', 59.90, 54.90, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300', 'Massa, brigadeiro, morango', 20, 1, 1, 28),

-- BORDAS RECHEADAS (subcategoria 29) - 3 produtos
(18, 29, 'simples', 'Borda Recheada com Catupiry 🧀', 'Adicional de borda recheada com catupiry', 'borda-catupiry', 8.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Catupiry', 2, 1, 1, 29),
(18, 29, 'simples', 'Borda Recheada com Cheddar 🧀', 'Adicional de borda recheada com cheddar', 'borda-cheddar', 8.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Cheddar', 2, 1, 1, 30),
(18, 29, 'simples', 'Borda Recheada com Chocolate 🍫', 'Adicional de borda recheada com chocolate', 'borda-chocolate', 9.90, NULL, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300', 'Chocolate', 2, 1, 0, 31),

-- BEBIDAS (subcategoria 14) - 4 produtos
(18, 14, 'simples', 'Refrigerante 2L 🥤', 'Coca-Cola, Guaraná, Sprite', 'refrigerante-2l', 14.90, NULL, 'https://images.unsplash.com/photo-1581006852262-4305c2fad871?w=300', 'Refrigerante', 2, 1, 1, 32),
(18, 14, 'simples', 'Refrigerante 1L 🥤', 'Coca-Cola, Guaraná, Sprite', 'refrigerante-1l', 10.90, NULL, 'https://images.unsplash.com/photo-1581006852262-4305c2fad871?w=300', 'Refrigerante', 2, 1, 0, 33),
(18, 14, 'simples', 'Suco Natural (500ml) 🧃', 'Laranja, limão, abacaxi', 'suco-500', 12.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Fruta natural', 5, 1, 0, 34),
(18, 14, 'simples', 'Cerveja Long Neck (355ml) 🍺', 'Heineken, Stella', 'cerveja-long', 9.90, NULL, 'https://images.unsplash.com/photo-1586994496097-9c0fb7a509ac?w=300', 'Cerveja', 2, 1, 1, 35);


-- =====================================================
-- LOJA 19: Açaí & Cia (20 produtos - açaí e sobremesas geladas)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- AÇAÍS (subcategoria 30) - 8 produtos
(19, 30, 'personalizavel', 'Açaí Pequeno (300ml) 🍧', 'Açaí puro, xarope de guaraná', 'acai-300', 16.90, 14.90, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí, xarope de guaraná', 3, 1, 1, 1),
(19, 30, 'personalizavel', 'Açaí Médio (500ml) 🍧', 'Açaí puro, xarope de guaraná', 'acai-500', 22.90, 19.90, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí, xarope de guaraná', 4, 1, 1, 2),
(19, 30, 'personalizavel', 'Açaí Grande (700ml) 🍧', 'Açaí puro, xarope de guaraná', 'acai-700', 28.90, 24.90, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí, xarope de guaraná', 5, 1, 1, 3),
(19, 30, 'personalizavel', 'Açaí com Banana (500ml) 🍌', 'Açaí com banana', 'acai-banana-500', 24.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí, banana', 4, 1, 0, 4),
(19, 30, 'personalizavel', 'Açaí com Morango (500ml) 🍓', 'Açaí com morango', 'acai-morango-500', 26.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí, morango', 4, 1, 0, 5),
(19, 30, 'personalizavel', 'Açaí Power (500ml) 💪', 'Açaí com banana, morango, granola, leite em pó', 'acai-power-500', 29.90, 26.90, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí, banana, morango, granola, leite em pó', 5, 1, 1, 6),
(19, 30, 'personalizavel', 'Açaí Fit (500ml) 🌱', 'Açaí zero açúcar, banana, granola sem açúcar', 'acai-fit-500', 27.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí zero, banana, granola zero', 4, 1, 0, 7),
(19, 30, 'simples', 'Açaí no Pote (1kg) 🪣', 'Açaí para levar para casa', 'acai-1kg', 45.90, 39.90, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí puro', 2, 1, 1, 8),

-- TOPPINGS (subcategoria 31) - 6 produtos
(19, 31, 'simples', 'Banana 🍌', 'Adicional de banana fatiada', 'top-banana', 2.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Banana', 1, 1, 1, 9),
(19, 31, 'simples', 'Morango 🍓', 'Adicional de morango picado', 'top-morango', 3.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Morango', 1, 1, 1, 10),
(19, 31, 'simples', 'Granola 🌾', 'Adicional de granola crocante', 'top-granola', 2.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Granola', 1, 1, 1, 11),
(19, 31, 'simples', 'Leite em Pó 🥛', 'Adicional de leite em pó', 'top-leite', 2.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Leite em pó', 1, 1, 0, 12),
(19, 31, 'simples', 'Leite Condensado 🥫', 'Adicional de leite condensado', 'top-leite-condensado', 3.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Leite condensado', 1, 1, 1, 13),
(19, 31, 'simples', 'Confete 🎉', 'Adicional de confete colorido', 'top-confete', 2.90, NULL, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Confete', 1, 1, 0, 14),

-- SORVETES (subcategoria 32) - 6 produtos
(19, 32, 'simples', 'Sorvete de Chocolate (2 bolas) 🍫', 'Sorvete de chocolate', 'sorvete-chocolate-2', 14.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Sorvete de chocolate', 3, 1, 1, 15),
(19, 32, 'simples', 'Sorvete de Morango (2 bolas) 🍓', 'Sorvete de morango', 'sorvete-morango-2', 14.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Sorvete de morango', 3, 1, 0, 16),
(19, 32, 'simples', 'Sorvete de Creme (2 bolas) 🍦', 'Sorvete de creme', 'sorvete-creme-2', 14.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Sorvete de creme', 3, 1, 0, 17),
(19, 32, 'simples', 'Sorvete Misto (2 bolas) 🍦', 'Duas bolas de sabores diferentes', 'sorvete-misto-2', 16.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Sorvete', 3, 1, 1, 18),
(19, 32, 'simples', 'Milkshake de Chocolate (400ml) 🥤', 'Milkshake cremoso de chocolate', 'milkshake-chocolate', 18.90, 16.90, 'https://images.unsplash.com/photo-1572490122744-4ab00e582f71?w=300', 'Sorvete, leite, calda de chocolate', 5, 1, 1, 19),
(19, 32, 'simples', 'Milkshake de Morango (400ml) 🥤', 'Milkshake cremoso de morango', 'milkshake-morango', 18.90, NULL, 'https://images.unsplash.com/photo-1572490122744-4ab00e582f71?w=300', 'Sorvete, leite, calda de morango', 5, 1, 0, 20);


-- =====================================================
-- LOJA 20: Sushi House (20 produtos - culinária japonesa)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- COMBINADOS (subcategoria 33) - 4 produtos
(20, 33, 'combo', 'Combinado Simples (10 peças) 🍣', '5 salmão, 5 atum', 'combo-simples-10', 34.90, 29.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão, atum, arroz, alga', 15, 1, 1, 1),
(20, 33, 'combo', 'Combinado Especial (15 peças) 🍣', '8 salmão, 7 atum', 'combo-especial-15', 49.90, 44.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão, atum, arroz, alga', 20, 1, 1, 2),
(20, 33, 'combo', 'Combinado Família (25 peças) 👪', '12 salmão, 8 atum, 5 kani', 'combo-familia-25', 79.90, 69.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão, atum, kani, arroz, alga', 25, 1, 1, 3),
(20, 33, 'combo', 'Combinado Premium (12 peças) ✨', 'Peças selecionadas com salmão grelhado', 'combo-premium-12', 59.90, 54.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão grelhado, salmão, atum, cream cheese', 20, 1, 1, 4),

-- SUSHIS (subcategoria 34) - 8 produtos
(20, 34, 'simples', 'Salmão (2 unidades) 🍣', 'Niguiri de salmão', 'salmao-2', 12.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão, arroz', 8, 1, 1, 5),
(20, 34, 'simples', 'Atum (2 unidades) 🍣', 'Niguiri de atum', 'atum-2', 13.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Atum, arroz', 8, 1, 0, 6),
(20, 34, 'simples', 'Kani (2 unidades) 🦀', 'Niguiri de kani', 'kani-2', 11.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Kani, arroz', 8, 1, 0, 7),
(20, 34, 'simples', 'Peixe Branco (2 unidades) 🐟', 'Niguiri de peixe branco', 'peixe-branco-2', 12.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Peixe branco, arroz', 8, 1, 0, 8),

-- TEMAKIS (subcategoria 35) - 4 produtos
(20, 35, 'personalizavel', 'Temaki Salmão (1 unidade) 🌯', 'Cone de alga com salmão, arroz, cream cheese', 'temaki-salmao', 19.90, 17.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão, arroz, cream cheese, alga', 10, 1, 1, 9),
(20, 35, 'personalizavel', 'Temaki Atum (1 unidade) 🌯', 'Cone de alga com atum, arroz, cream cheese', 'temaki-atum', 21.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Atum, arroz, cream cheese, alga', 10, 1, 0, 10),
(20, 35, 'personalizavel', 'Temaki Salmão Grelhado (1 unidade) 🔥', 'Cone de alga com salmão grelhado, arroz, cream cheese', 'temaki-salmao-grelhado', 24.90, 21.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão grelhado, arroz, cream cheese, alga', 12, 1, 1, 11),
(20, 35, 'personalizavel', 'Temaki Skin (1 unidade) 🐟', 'Cone de alga com pele de salmão crocante', 'temaki-skin', 22.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Pele de salmão, arroz, cream cheese, alga', 12, 1, 0, 12),

-- URAMAKIS (subcategoria 36) - 4 produtos
(20, 36, 'simples', 'Uramaki Filadélfia (8 unidades) 🥢', 'Salmão, cream cheese, arroz, gergelim', 'uramaki-filadelfia-8', 28.90, 24.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão, cream cheese, arroz, gergelim', 12, 1, 1, 13),
(20, 36, 'simples', 'Uramaki Califórnia (8 unidades) 🥑', 'Kani, pepino, manga, arroz, gergelim', 'uramaki-california-8', 26.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Kani, pepino, manga, arroz, gergelim', 12, 1, 0, 14),
(20, 36, 'simples', 'Uramaki Salmão Grelhado (8 unidades) 🔥', 'Salmão grelhado, cream cheese, arroz, gergelim', 'uramaki-salmao-grelhado-8', 32.90, 29.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão grelhado, cream cheese, arroz, gergelim', 15, 1, 1, 15),
(20, 36, 'simples', 'Uramaki Hot Filadélfia (8 unidades) 🔥', 'Uramaki empanado e frito', 'uramaki-hot-8', 34.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Salmão, cream cheese, arroz, farinha, óleo', 15, 1, 1, 16),

-- ENTRADAS (subcategoria 37) - 4 produtos
(20, 37, 'simples', 'Missoshiru (300ml) 🥣', 'Sopa de missô com tofu e cebolinha', 'missoshiru-300', 9.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Missô, tofu, cebolinha', 5, 1, 1, 17),
(20, 37, 'simples', 'Sunomono (150g) 🥒', 'Salada de pepino com molho agridoce', 'sunomono-150', 12.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Pepino, molho agridoce, gergelim', 5, 1, 0, 18),
(20, 37, 'simples', 'Harumaki (2 unidades) 🥟', 'Rolinho primavera de legumes', 'harumaki-2', 11.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Legumes, massa', 8, 1, 0, 19),
(20, 37, 'simples', 'Shimeji na Chapa (150g) 🍄', 'Shimeji refogado na chapa', 'shimeji-150', 24.90, 21.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Shimeji, molho shoyu', 10, 1, 1, 20);

-- =====================================================
-- LOJA 21: Donuts & Cia (20 produtos - donuts e doces)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- DONUTS CLÁSSICOS (subcategoria 38) - 8 produtos
(21, 38, 'simples', 'Donut de Chocolate (1 unidade) 🍩', 'Donut com cobertura de chocolate', 'donut-chocolate-1', 8.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, chocolate, granulado', 3, 1, 1, 1),
(21, 38, 'simples', 'Donut de Chocolate (4 unidades) 🍩🍩', '4 donuts com cobertura de chocolate', 'donut-chocolate-4', 29.90, 26.90, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, chocolate, granulado', 8, 1, 1, 2),
(21, 38, 'simples', 'Donut de Morango (1 unidade) 🍓', 'Donut com cobertura de morango', 'donut-morango-1', 8.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, cobertura de morango, confeitos', 3, 1, 0, 3),
(21, 38, 'simples', 'Donut de Morango (4 unidades) 🍓🍩', '4 donuts com cobertura de morango', 'donut-morango-4', 29.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, cobertura de morango, confeitos', 8, 1, 0, 4),
(21, 38, 'simples', 'Donut de Doce de Leite (1 unidade) 🥛', 'Donut com cobertura de doce de leite', 'donut-doce-leite-1', 9.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, doce de leite', 3, 1, 1, 5),
(21, 38, 'simples', 'Donut de Doce de Leite (4 unidades) 🥛🍩', '4 donuts com cobertura de doce de leite', 'donut-doce-leite-4', 32.90, 29.90, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, doce de leite', 8, 1, 1, 6),
(21, 38, 'simples', 'Donut de Baunilha (1 unidade) 🍦', 'Donut com cobertura de baunilha e confeitos', 'donut-baunilha-1', 8.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, baunilha, confeitos', 3, 1, 0, 7),
(21, 38, 'simples', 'Donut de Baunilha (4 unidades) 🍦🍩', '4 donuts com cobertura de baunilha', 'donut-baunilha-4', 29.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, baunilha, confeitos', 8, 1, 0, 8),

-- DONUTS ESPECIAIS (subcategoria 38) - 6 produtos
(21, 38, 'simples', 'Donut Recheado de Chocolate (1 unidade) 🍫', 'Donut recheado com chocolate', 'donut-recheado-chocolate-1', 11.90, 9.90, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, recheio de chocolate, cobertura', 4, 1, 1, 9),
(21, 38, 'simples', 'Donut Recheado de Doce de Leite (1 unidade) 🥛', 'Donut recheado com doce de leite', 'donut-recheado-doce-leite-1', 11.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, doce de leite, cobertura', 4, 1, 1, 10),
(21, 38, 'simples', 'Donut Recheado de Morango (1 unidade) 🍓', 'Donut recheado com geleia de morango', 'donut-recheado-morango-1', 11.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, geleia de morango, cobertura', 4, 1, 0, 11),
(21, 38, 'simples', 'Donut de Oreo (1 unidade) 🍪', 'Donut com cobertura e pedaços de Oreo', 'donut-oreo-1', 12.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, chocolate, Oreo', 4, 1, 1, 12),
(21, 38, 'simples', 'Donut de Paçoca (1 unidade) 🥜', 'Donut com cobertura e farofa de paçoca', 'donut-pacoca-1', 12.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, chocolate, paçoca', 4, 1, 1, 13),
(21, 38, 'simples', 'Donut Red Velvet (1 unidade) ❤️', 'Donut red velvet com cream cheese', 'donut-red-velvet-1', 13.90, 11.90, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa red velvet, cream cheese, cobertura', 5, 1, 1, 14),

-- MINI DONUTS (subcategoria 38) - 2 produtos
(21, 38, 'simples', 'Mini Donuts (6 unidades) 🍩', '6 mini donuts sortidos', 'mini-donuts-6', 19.90, 16.90, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Mini donuts sortidos', 5, 1, 1, 15),
(21, 38, 'simples', 'Mini Donuts (12 unidades) 🍩🍩', '12 mini donuts sortidos', 'mini-donuts-12', 34.90, 29.90, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Mini donuts sortidos', 8, 1, 1, 16),

-- BEBIDAS (subcategoria 14) - 2 produtos
(21, 14, 'simples', 'Café Expresso (50ml) ☕', 'Café expresso', 'cafe-expresso-50', 4.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café', 2, 1, 1, 17),
(21, 14, 'simples', 'Cappuccino (300ml) 🥤', 'Cappuccino cremoso', 'cappuccino-300', 8.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite, chocolate', 4, 1, 1, 18),

-- KITS ESPECIAIS (subcategoria 38) - 2 produtos
(21, 38, 'combo', 'Kit Donuts (6 unidades) 🎁', '6 donuts sortidos', 'kit-donuts-6', 42.90, 37.90, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', '6 donuts variados', 8, 1, 1, 19),
(21, 38, 'combo', 'Kit Donuts (12 unidades) 🎁🎁', '12 donuts sortidos', 'kit-donuts-12', 79.90, 69.90, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', '12 donuts variados', 12, 1, 1, 20);


-- =====================================================
-- LOJA 22: Saladas & Greens (20 produtos - comidas saudáveis e bowls)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- SALADAS COMPLETAS (subcategoria 19) - 8 produtos
(22, 19, 'simples', 'Salada Caesar Tradicional 🥗', 'Alface romana, frango grelhado, croutons, parmesão, molho caesar', 'salada-caesar', 28.90, 24.90, 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=300', 'Alface, frango, croutons, parmesão, molho caesar', 8, 1, 1, 1),
(22, 19, 'simples', 'Salada Mediterrânea 🇬🇷', 'Rúcula, tomate cereja, pepino, azeitona preta, queijo feta, orégano', 'salada-mediterranea', 32.90, 29.90, 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=300', 'Rúcula, tomate, pepino, azeitona, queijo feta', 7, 1, 1, 2),
(22, 19, 'simples', 'Salada de Quinoa com Legumes 🌾', 'Quinoa, cenoura, beterraba, ervilha, milho, salsinha', 'salada-quinoa', 26.90, NULL, 'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=300', 'Quinoa, cenoura, beterraba, ervilha, milho', 6, 1, 1, 3),
(22, 19, 'simples', 'Salada Tropical com Manga 🥭', 'Mix de folhas, manga, abacaxi, castanhas, molho de maracujá', 'salada-tropical', 29.90, 26.90, 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=300', 'Folhas, manga, abacaxi, castanha, molho de maracujá', 6, 1, 1, 4),
(22, 19, 'simples', 'Salada de Grãos com Abacate 🥑', 'Grão de bico, lentilha, abacate, tomate, cebola roxa, coentro', 'salada-graos-abacate', 31.90, NULL, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300', 'Grão de bico, lentilha, abacate, tomate, cebola', 6, 1, 0, 5),
(22, 19, 'simples', 'Salada de Beterraba Assada 🔴', 'Beterraba assada, rúcula, queijo de cabra, nozes, mel', 'salada-beterraba', 33.90, NULL, 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=300', 'Beterraba, rúcula, queijo de cabra, nozes, mel', 8, 1, 1, 6),
(22, 19, 'simples', 'Salada de Frango com Mostarda e Mel 🍯', 'Frango desfiado, mix de folhas, tomate, molho mostarda e mel', 'salada-frango-mel', 30.90, 27.90, 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=300', 'Frango, folhas, tomate, molho mostarda e mel', 7, 1, 1, 7),
(22, 19, 'simples', 'Salada Veggie Power 🌱', 'Couve, quinoa, grão de bico, cenoura, beterraba, sementes de abóbora', 'salada-veggie', 27.90, NULL, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300', 'Couve, quinoa, grão de bico, cenoura, beterraba, sementes', 6, 1, 0, 8),

-- BOWLS (subcategoria 20) - 6 produtos
(22, 20, 'simples', 'Bowl de Frango Grelhado 🐔', 'Arroz integral, frango grelhado, brócolis, cenoura, ovo', 'bowl-frango', 32.90, 29.90, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300', 'Arroz integral, frango, brócolis, cenoura, ovo', 10, 1, 1, 9),
(22, 20, 'simples', 'Bowl de Salmão com Gengibre 🐟', 'Arroz de couve-flor, salmão grelhado, aspargos, gengibre', 'bowl-salmao', 42.90, 38.90, 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=300', 'Arroz de couve-flor, salmão, aspargos, gengibre', 12, 1, 1, 10),
(22, 20, 'simples', 'Bowl de Quinoa com Legumes 🌿', 'Quinoa, abobrinha, berinjela, pimentão, molho de iogurte', 'bowl-quinoa', 28.90, NULL, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300', 'Quinoa, abobrinha, berinjela, pimentão, iogurte', 8, 1, 1, 11),
(22, 20, 'simples', 'Bowl de Carne com Batata Doce 🥩', 'Arroz integral, carne desfiada, batata doce, couve refogada', 'bowl-carne', 35.90, 32.90, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=300', 'Arroz integral, carne, batata doce, couve', 12, 1, 1, 12),
(22, 20, 'simples', 'Bowl Vegano de Grão de Bico 🫘', 'Grão de bico temperado, arroz integral, legumes assados, tahine', 'bowl-vegano', 29.90, NULL, 'https://images.unsplash.com/photo-1515543904379-3d757f7a6e4e?w=300', 'Grão de bico, arroz integral, legumes, tahine', 8, 1, 0, 13),
(22, 20, 'simples', 'Bowl de Ovos Mexidos com Abacate 🥑', 'Ovos mexidos, abacate, tomate, pão integral torrado', 'bowl-ovos', 26.90, NULL, 'https://images.unsplash.com/photo-1510693206972-df098062cb71?w=300', 'Ovos, abacate, tomate, pão integral', 7, 1, 0, 14),

-- WRAPS (subcategoria 20) - 3 produtos
(22, 20, 'simples', 'Wrap de Frango com Rúcula 🌯', 'Tortilha integral, frango, rúcula, tomate seco, cream cheese', 'wrap-frango', 24.90, 21.90, 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=300', 'Tortilha, frango, rúcula, tomate seco, cream cheese', 6, 1, 1, 15),
(22, 20, 'simples', 'Wrap Vegano de Homus 🌯', 'Tortilha integral, homus, cenoura, pepino, rúcula', 'wrap-vegano', 22.90, NULL, 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=300', 'Tortilha, homus, cenoura, pepino, rúcula', 6, 1, 0, 16),
(22, 20, 'simples', 'Wrap de Salmão com Cream Cheese 🌯', 'Tortilha integral, salmão defumado, cream cheese, alface, pepino', 'wrap-salmao', 32.90, 29.90, 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=300', 'Tortilha, salmão, cream cheese, alface, pepino', 6, 1, 1, 17),

-- SUCOS DETOX (subcategoria 14) - 3 produtos
(22, 14, 'simples', 'Suco Verde (500ml) 🥬', 'Couve, limão, maçã, gengibre, água de coco', 'suco-verde-500', 16.90, 14.90, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Couve, limão, maçã, gengibre, água de coco', 3, 1, 1, 18),
(22, 14, 'simples', 'Suco Detox (500ml) 🥒', 'Pepino, couve, limão, hortelã, gengibre', 'suco-detox-500', 17.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Pepino, couve, limão, hortelã, gengibre', 3, 1, 1, 19),
(22, 14, 'simples', 'Suco Energético (500ml) 🍊', 'Laranja, cenoura, beterraba, gengibre', 'suco-energetico-500', 18.90, 16.90, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Laranja, cenoura, beterraba, gengibre', 3, 1, 1, 20);


-- =====================================================
-- LOJA 23: Padaria da Vila (20 produtos - padaria e café da manhã)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PÃES (subcategoria 39) - 6 produtos
(23, 39, 'simples', 'Pão Francês (unidade) 🥖', 'Pão francês tradicional', 'pao-frances-1', 0.90, NULL, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Farinha, fermento, sal', 2, 1, 1, 1),
(23, 39, 'simples', 'Pão Francês (6 unidades) 🥖🥖', '6 pães franceses', 'pao-frances-6', 4.90, 4.50, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Farinha, fermento, sal', 5, 1, 1, 2),
(23, 39, 'simples', 'Pão Francês (12 unidades) 🥖🥖🥖', '12 pães franceses', 'pao-frances-12', 9.50, 8.90, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Farinha, fermento, sal', 8, 1, 1, 3),
(23, 39, 'simples', 'Pão de Forma (400g) 🍞', 'Pão de forma tradicional', 'pao-forma-400', 7.90, NULL, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Farinha, fermento, sal, açúcar', 3, 1, 0, 4),
(23, 39, 'simples', 'Pão Integral (400g) 🌾', 'Pão integral com grãos', 'pao-integral-400', 9.90, NULL, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Farinha integral, grãos, fermento', 3, 1, 0, 5),
(23, 39, 'simples', 'Baguete (unidade) 🥖', 'Baguete francesa', 'baguete-1', 4.90, NULL, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Farinha, fermento, sal', 3, 1, 1, 6),

-- SALGADOS (subcategoria 25) - 6 produtos
(23, 25, 'simples', 'Pão de Queijo (unidade) 🧀', 'Pão de queijo mineiro', 'pao-queijo-1', 2.90, NULL, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Polvilho, queijo, ovos', 3, 1, 1, 7),
(23, 25, 'simples', 'Pão de Queijo (6 unidades) 🧀🧀', '6 pães de queijo', 'pao-queijo-6', 15.90, 13.90, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Polvilho, queijo, ovos', 8, 1, 1, 8),
(23, 25, 'simples', 'Coxinha de Frango (unidade) 🥟', 'Coxinha de frango com catupiry', 'coxinha-1', 5.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Frango, catupiry, massa', 4, 1, 1, 9),
(23, 25, 'simples', 'Coxinha de Frango (6 unidades) 🥟🥟', '6 coxinhas de frango', 'coxinha-6', 32.90, 29.90, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Frango, catupiry, massa', 15, 1, 1, 10),
(23, 25, 'simples', 'Empada de Frango (unidade) 🥧', 'Empada de frango', 'empada-frango-1', 5.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Frango, massa podre', 4, 1, 0, 11),
(23, 25, 'simples', 'Empada de Palmito (unidade) 🌴', 'Empada de palmito', 'empada-palmito-1', 6.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Palmito, massa podre', 4, 1, 0, 12),

-- DOCES (subcategoria 26) - 4 produtos
(23, 26, 'simples', 'Sonho de Chocolate (unidade) 😋', 'Sonho recheado com chocolate', 'sonho-chocolate-1', 7.90, NULL, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Massa, chocolate, açúcar', 4, 1, 1, 13),
(23, 26, 'simples', 'Sonho de Doce de Leite (unidade) 🥛', 'Sonho recheado com doce de leite', 'sonho-doce-leite-1', 7.90, NULL, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Massa, doce de leite', 4, 1, 1, 14),
(23, 26, 'simples', 'Bolinho de Chuva (6 unidades) ☔', 'Bolinho de chuva polvilhado com açúcar e canela', 'bolinho-chuva-6', 11.90, 9.90, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Farinha, ovos, açúcar, canela', 6, 1, 1, 15),
(23, 26, 'simples', 'Pastel de Nata (unidade) 🇵🇹', 'Pastel de nata português', 'pastel-nata-1', 6.90, NULL, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Massa folhada, creme', 4, 1, 1, 16),

-- BEBIDAS QUENTES (subcategoria 14) - 4 produtos
(23, 14, 'simples', 'Café Expresso (50ml) ☕', 'Café expresso', 'cafe-expresso-50', 3.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café', 2, 1, 1, 17),
(23, 14, 'simples', 'Café com Leite (200ml) ☕🥛', 'Café com leite', 'cafe-leite-200', 5.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café, leite', 3, 1, 1, 18),
(23, 14, 'simples', 'Cappuccino (250ml) 🥤', 'Cappuccino cremoso', 'cappuccino-250', 8.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite, chocolate', 4, 1, 1, 19),
(23, 14, 'simples', 'Chocolate Quente (250ml) 🍫', 'Chocolate quente cremoso', 'chocolate-quente-250', 9.90, 8.90, 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=300', 'Leite, chocolate, chantilly', 4, 1, 1, 20);


-- =====================================================
-- LOJA 24: Sorveteria Gelatto (20 produtos - sorvetes artesanais)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- SORVETES DE MASSA (subcategoria 32) - 10 produtos
(24, 32, 'simples', 'Sorvete de Chocolate (1 bola) 🍫', 'Sorvete de chocolate belga', 'sorvete-chocolate-1', 7.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Chocolate belga, leite, creme', 2, 1, 1, 1),
(24, 32, 'simples', 'Sorvete de Chocolate (2 bolas) 🍫🍫', 'Sorvete de chocolate belga', 'sorvete-chocolate-2', 13.90, 11.90, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Chocolate belga, leite, creme', 3, 1, 1, 2),
(24, 32, 'simples', 'Sorvete de Morango (1 bola) 🍓', 'Sorvete de morango com pedaços', 'sorvete-morango-1', 7.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Morango, leite, creme', 2, 1, 0, 3),
(24, 32, 'simples', 'Sorvete de Morango (2 bolas) 🍓🍓', 'Sorvete de morango com pedaços', 'sorvete-morango-2', 13.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Morango, leite, creme', 3, 1, 0, 4),
(24, 32, 'simples', 'Sorvete de Creme (1 bola) 🍦', 'Sorvete de creme tradicional', 'sorvete-creme-1', 6.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Leite, creme, açúcar', 2, 1, 0, 5),
(24, 32, 'simples', 'Sorvete de Creme (2 bolas) 🍦🍦', 'Sorvete de creme tradicional', 'sorvete-creme-2', 12.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Leite, creme, açúcar', 3, 1, 0, 6),
(24, 32, 'simples', 'Sorvete de Flocos (1 bola) 🎉', 'Sorvete de flocos com confetes', 'sorvete-flocos-1', 7.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Leite, creme, confetes', 2, 1, 1, 7),
(24, 32, 'simples', 'Sorvete de Flocos (2 bolas) 🎉🎉', 'Sorvete de flocos com confetes', 'sorvete-flocos-2', 13.90, 11.90, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Leite, creme, confetes', 3, 1, 1, 8),
(24, 32, 'simples', 'Sorvete de Pistache (1 bola) 💚', 'Sorvete de pistache italiano', 'sorvete-pistache-1', 9.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Pistache, leite, creme', 2, 1, 1, 9),
(24, 32, 'simples', 'Sorvete de Pistache (2 bolas) 💚💚', 'Sorvete de pistache italiano', 'sorvete-pistache-2', 17.90, 15.90, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Pistache, leite, creme', 3, 1, 1, 10),

-- SORVETES ESPECIAIS (subcategoria 32) - 4 produtos
(24, 32, 'simples', 'Sorvete de Açaí (300ml) 🍧', 'Açaí cremoso', 'acai-300', 15.90, 13.90, 'https://images.unsplash.com/photo-1590301157412-b0fe86a9083c?w=300', 'Açaí, xarope de guaraná', 3, 1, 1, 11),
(24, 32, 'simples', 'Sorvete de Coco com Calda de Chocolate 🥥', 'Sorvete de coco com calda de chocolate', 'sorvete-coco-chocolate', 16.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Coco, leite, chocolate', 3, 1, 1, 12),
(24, 32, 'simples', 'Sorvete de Doce de Leite com Nozes 🥛', 'Sorvete de doce de leite com nozes', 'sorvete-doce-leite-nozes', 17.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Doce de leite, nozes, creme', 3, 1, 0, 13),
(24, 32, 'simples', 'Sorvete de Limão Siciliano (1 bola) 🍋', 'Sorvete de limão siciliano', 'sorvete-limao-1', 7.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Limão siciliano, leite, creme', 2, 1, 0, 14),

-- FROZEN YOGURT (subcategoria 32) - 2 produtos
(24, 32, 'simples', 'Frozen Yogurt de Morango (300ml) 🍓', 'Iogurte gelado sabor morango', 'frozen-morango-300', 14.90, 12.90, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Iogurte, morango', 3, 1, 1, 15),
(24, 32, 'simples', 'Frozen Yogurt de Frutas Vermelhas (300ml) 🫐', 'Iogurte gelado com frutas vermelhas', 'frozen-frutas-300', 15.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Iogurte, frutas vermelhas', 3, 1, 0, 16),

-- TOPPINGS (subcategoria 31) - 4 produtos
(24, 31, 'simples', 'Calda de Chocolate (50ml) 🍫', 'Calda de chocolate', 'calda-chocolate-50', 3.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Chocolate', 1, 1, 1, 17),
(24, 31, 'simples', 'Calda de Morango (50ml) 🍓', 'Calda de morango', 'calda-morango-50', 3.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Morango', 1, 1, 0, 18),
(24, 31, 'simples', 'Granulado Colorido (20g) 🎨', 'Granulado colorido', 'granulado-20', 2.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Açúcar, corantes', 1, 1, 0, 19),
(24, 31, 'simples', 'Castanhas Picadas (20g) 🥜', 'Castanhas picadas', 'castanhas-20', 4.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Castanhas', 1, 1, 1, 20);


-- =====================================================
-- LOJA 25: Pastelaria do Zé (20 produtos - pastéis e caldos)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PASTÉIS SALGADOS (subcategoria 40) - 10 produtos
(25, 40, 'simples', 'Pastel de Carne (unidade) 🥩', 'Pastel de carne moída', 'pastel-carne-1', 7.90, 6.90, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, carne moída, cebola', 5, 1, 1, 1),
(25, 40, 'simples', 'Pastel de Carne (6 unidades) 🥩🥟', '6 pastéis de carne', 'pastel-carne-6', 42.90, 37.90, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, carne moída, cebola', 15, 1, 1, 2),
(25, 40, 'simples', 'Pastel de Queijo (unidade) 🧀', 'Pastel de queijo derretido', 'pastel-queijo-1', 7.90, NULL, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, mussarela', 5, 1, 1, 3),
(25, 40, 'simples', 'Pastel de Queijo (6 unidades) 🧀🥟', '6 pastéis de queijo', 'pastel-queijo-6', 42.90, 37.90, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, mussarela', 15, 1, 1, 4),
(25, 40, 'simples', 'Pastel de Frango (unidade) 🐔', 'Pastel de frango com catupiry', 'pastel-frango-1', 8.90, NULL, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, frango, catupiry', 5, 1, 1, 5),
(25, 40, 'simples', 'Pastel de Frango (6 unidades) 🐔🥟', '6 pastéis de frango', 'pastel-frango-6', 46.90, 41.90, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, frango, catupiry', 15, 1, 1, 6),
(25, 40, 'simples', 'Pastel de Pizza (unidade) 🍕', 'Pastel de pizza (queijo, presunto, orégano)', 'pastel-pizza-1', 8.90, NULL, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, queijo, presunto, orégano', 5, 1, 0, 7),
(25, 40, 'simples', 'Pastel de Calabresa (unidade) 🌶️', 'Pastel de calabresa com cebola', 'pastel-calabresa-1', 8.90, NULL, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, calabresa, cebola', 5, 1, 0, 8),
(25, 40, 'simples', 'Pastel de Palmito (unidade) 🌴', 'Pastel de palmito', 'pastel-palmito-1', 9.90, NULL, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, palmito, catupiry', 5, 1, 1, 9),
(25, 40, 'simples', 'Pastel Misto (unidade) 🥟', 'Pastel de carne e queijo', 'pastel-misto-1', 8.90, 7.90, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, carne, queijo', 5, 1, 1, 10),

-- PASTÉIS DOCES (subcategoria 41) - 4 produtos
(25, 41, 'simples', 'Pastel de Banana com Canela (unidade) 🍌', 'Pastel de banana com canela e açúcar', 'pastel-banana-1', 8.90, NULL, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, banana, canela, açúcar', 5, 1, 1, 11),
(25, 41, 'simples', 'Pastel de Banana com Canela (4 unidades) 🍌🥟', '4 pastéis de banana', 'pastel-banana-4', 32.90, 29.90, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, banana, canela, açúcar', 12, 1, 1, 12),
(25, 41, 'simples', 'Pastel de Chocolate (unidade) 🍫', 'Pastel de chocolate', 'pastel-chocolate-1', 9.90, NULL, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, chocolate', 5, 1, 1, 13),
(25, 41, 'simples', 'Pastel de Doce de Leite (unidade) 🥛', 'Pastel de doce de leite', 'pastel-doce-leite-1', 9.90, NULL, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, doce de leite', 5, 1, 0, 14),

-- CALDOS (subcategoria 21) - 4 produtos
(25, 21, 'simples', 'Caldo Verde (500ml) 🥣', 'Caldo verde com couve e calabresa', 'caldo-verde-500', 18.90, 16.90, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Batata, couve, calabresa', 8, 1, 1, 15),
(25, 21, 'simples', 'Caldo de Mandioca com Carne (500ml) 🥔', 'Caldo de mandioca com carne', 'caldo-mandioca-500', 21.90, 18.90, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Mandioca, carne, legumes', 8, 1, 1, 16),
(25, 21, 'simples', 'Caldo de Feijão (500ml) 🫘', 'Caldo de feijão com bacon', 'caldo-feijao-500', 19.90, NULL, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Feijão, bacon, calabresa', 8, 1, 0, 17),
(25, 21, 'simples', 'Caldo de Cana (500ml) 🥤', 'Caldo de cana puro', 'caldo-cana-500', 8.90, NULL, 'https://images.unsplash.com/photo-1581006852262-4305c2fad871?w=300', 'Cana-de-açúcar', 3, 1, 1, 18),

-- BEBIDAS (subcategoria 14) - 2 produtos
(25, 14, 'simples', 'Suco de Laranja (500ml) 🍊', 'Suco de laranja natural', 'suco-laranja-500', 10.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Laranja', 3, 1, 1, 19),
(25, 14, 'simples', 'Refrigerante Lata (350ml) 🥤', 'Coca-Cola, Guaraná, Sprite', 'refrigerante-lata-350', 5.90, NULL, 'https://images.unsplash.com/photo-1581006852262-4305c2fad871?w=300', 'Refrigerante', 2, 1, 0, 20);



-- =====================================================
-- LOJA 26: Cantina da Nonna (20 produtos - comida italiana)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- MASSAS (subcategoria 42) - 8 produtos
(26, 42, 'simples', 'Espaguete à Bolonhesa (individual) 🍝', 'Espaguete com molho bolonhesa', 'espaguete-bolonhesa', 28.90, 24.90, 'https://images.unsplash.com/photo-1622973536968-3ead9e780960?w=300', 'Espaguete, carne moída, molho de tomate, manjericão', 15, 1, 1, 1),
(26, 42, 'simples', 'Espaguete à Bolonhesa (família) 🍝👪', 'Espaguete com molho bolonhesa para 3 pessoas', 'espaguete-bolonhesa-familia', 69.90, 59.90, 'https://images.unsplash.com/photo-1622973536968-3ead9e780960?w=300', 'Espaguete, carne moída, molho de tomate, manjericão', 25, 1, 1, 2),
(26, 42, 'simples', 'Lasanha à Bolonhesa (individual) 🍝', 'Lasanha de carne com molho branco', 'lasanha-bolonhesa', 32.90, 28.90, 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=300', 'Massa, carne, molho bolonhesa, molho branco, queijo', 20, 1, 1, 3),
(26, 42, 'simples', 'Lasanha à Bolonhesa (família) 🍝👪', 'Lasanha para 3 pessoas', 'lasanha-bolonhesa-familia', 79.90, 69.90, 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=300', 'Massa, carne, molho bolonhesa, molho branco, queijo', 30, 1, 1, 4),
(26, 42, 'simples', 'Talharim ao Molho Branco com Camarão 🍤', 'Talharim com molho branco e camarões', 'talharim-camarao', 42.90, 37.90, 'https://images.unsplash.com/photo-1622973536968-3ead9e780960?w=300', 'Talharim, camarão, molho branco, parmesão', 20, 1, 1, 5),
(26, 42, 'simples', 'Ravioli de Ricota com Espinafre (individual) 🥟', 'Ravioli recheado com molho de tomate', 'ravioli-ricota', 34.90, NULL, 'https://images.unsplash.com/photo-1622973536968-3ead9e780960?w=300', 'Massa, ricota, espinafre, molho de tomate', 18, 1, 1, 6),
(26, 42, 'simples', 'Gnochi de Batata ao Sugo (individual) 🥔', 'Nhoque de batata com molho sugo', 'gnochi-sugo', 29.90, NULL, 'https://images.unsplash.com/photo-1622973536968-3ead9e780960?w=300', 'Batata, farinha, molho sugo, manjericão', 15, 1, 0, 7),
(26, 42, 'simples', 'Gnochi de Batata ao Sugo (família) 🥔👪', 'Nhoque para 3 pessoas', 'gnochi-sugo-familia', 74.90, 64.90, 'https://images.unsplash.com/photo-1622973536968-3ead9e780960?w=300', 'Batata, farinha, molho sugo, manjericão', 25, 1, 0, 8),

-- RISOTOS (subcategoria 42) - 4 produtos
(26, 42, 'simples', 'Risoto de Funghi (individual) 🍄', 'Risoto cremoso com cogumelos funghi', 'risoto-funghi', 38.90, 34.90, 'https://images.unsplash.com/photo-1535405102936-fbe5a3693bfd?w=300', 'Arroz arbóreo, funghi, parmesão, vinho branco', 20, 1, 1, 9),
(26, 42, 'simples', 'Risoto de Camarão (individual) 🍤', 'Risoto cremoso com camarões', 'risoto-camarao', 44.90, 39.90, 'https://images.unsplash.com/photo-1535405102936-fbe5a3693bfd?w=300', 'Arroz arbóreo, camarão, parmesão, vinho branco', 22, 1, 1, 10),
(26, 42, 'simples', 'Risoto de Limão Siciliano (individual) 🍋', 'Risoto cremoso com limão siciliano', 'risoto-limao', 34.90, NULL, 'https://images.unsplash.com/photo-1535405102936-fbe5a3693bfd?w=300', 'Arroz arbóreo, limão siciliano, parmesão', 18, 1, 0, 11),
(26, 42, 'simples', 'Risoto de Palmito (individual) 🌴', 'Risoto cremoso com palmito', 'risoto-palmito', 36.90, NULL, 'https://images.unsplash.com/photo-1535405102936-fbe5a3693bfd?w=300', 'Arroz arbóreo, palmito, parmesão', 18, 1, 0, 12),

-- CARNES (subcategoria 43) - 4 produtos
(26, 43, 'simples', 'Filé à Parmegiana (individual) 🥩', 'Filé empanado com molho e queijo', 'file-parmegiana', 42.90, 37.90, 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e5?w=300', 'Filé mignon, molho de tomate, mussarela, presunto', 20, 1, 1, 13),
(26, 43, 'simples', 'Filé à Parmegiana (família) 🥩👪', 'Filé para 3 pessoas', 'file-parmegiana-familia', 99.90, 89.90, 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e5?w=300', 'Filé mignon, molho de tomate, mussarela, presunto', 30, 1, 1, 14),
(26, 43, 'simples', 'Frango à Parmegiana (individual) 🐔', 'Frango empanado com molho e queijo', 'frango-parmegiana', 36.90, NULL, 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e5?w=300', 'Peito de frango, molho de tomate, mussarela', 18, 1, 0, 15),
(26, 43, 'simples', 'Bife à Milanesa (individual) 🥩', 'Bife empanado', 'bife-milanesa', 32.90, NULL, 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e5?w=300', 'Bife, farinha de rosca, ovos', 15, 1, 0, 16),

-- ENTRADAS E BEBIDAS (subcategoria 37 e 14) - 4 produtos
(26, 37, 'simples', 'Bruschetta (4 unidades) 🥖', 'Pão italiano com tomate, manjericão e azeite', 'bruschetta-4', 22.90, 19.90, 'https://images.unsplash.com/photo-1572695157366-5e585ab2b69f?w=300', 'Pão, tomate, manjericão, alho, azeite', 8, 1, 1, 17),
(26, 37, 'simples', 'Antepasto de Berinjela (200g) 🍆', 'Antepasto de berinjela', 'antepasto-berinjela', 24.90, NULL, 'https://images.unsplash.com/photo-1572695157366-5e585ab2b69f?w=300', 'Berinjela, azeitonas, pimentão, azeite', 5, 1, 0, 18),
(26, 14, 'simples', 'Vinho Tinto (taça) 🍷', 'Vinho tinto da casa', 'vinho-tinto-taca', 16.90, NULL, 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=300', 'Vinho tinto', 2, 1, 1, 19),
(26, 14, 'simples', 'Vinho Branco (taça) 🥂', 'Vinho branco da casa', 'vinho-branco-taca', 16.90, NULL, 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=300', 'Vinho branco', 2, 1, 1, 20);


-- =====================================================
-- LOJA 27: Comida Oriental (20 produtos - culinária chinesa)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PRATOS PRINCIPAIS (subcategoria 44) - 8 produtos
(27, 44, 'simples', 'Yakisoba de Frango (600ml) 🍜', 'Macarrão oriental com frango e legumes', 'yakisoba-frango', 32.90, 28.90, 'https://images.unsplash.com/photo-1569058242252-6df0bf1f03c5?w=300', 'Macarrão, frango, repolho, cenoura, brocólis, shoyu', 12, 1, 1, 1),
(27, 44, 'simples', 'Yakisoba de Carne (600ml) 🥩', 'Macarrão oriental com carne e legumes', 'yakisoba-carne', 34.90, 30.90, 'https://images.unsplash.com/photo-1569058242252-6df0bf1f03c5?w=300', 'Macarrão, carne, repolho, cenoura, brocólis, shoyu', 12, 1, 1, 2),
(27, 44, 'simples', 'Yakisoba Misto (600ml) 🥢', 'Macarrão oriental com frango, carne e legumes', 'yakisoba-misto', 36.90, 32.90, 'https://images.unsplash.com/photo-1569058242252-6df0bf1f03c5?w=300', 'Macarrão, frango, carne, legumes, shoyu', 15, 1, 1, 3),
(27, 44, 'simples', 'Frango Xadrez (500g) 🐔', 'Frango com legumes e molho agridoce', 'frango-xadrez', 34.90, NULL, 'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=300', 'Frango, pimentão, cebola, amendoim, molho agridoce', 12, 1, 1, 4),
(27, 44, 'simples', 'Carne com Brócolis (500g) 🥦', 'Carne fatiada com brócolis ao molho shoyu', 'carne-brocolis', 36.90, NULL, 'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=300', 'Carne, brócolis, shoyu, gengibre', 12, 1, 0, 5),
(27, 44, 'simples', 'Robalo Agridoce (600g) 🐟', 'Filé de robalo ao molho agridoce', 'robalo-agridoce', 42.90, 37.90, 'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=300', 'Robalo, molho agridoce, legumes', 15, 1, 1, 6),
(27, 44, 'simples', 'Porco Agridoce (500g) 🐷', 'Lombo de porco ao molho agridoce', 'porco-agridoce', 34.90, NULL, 'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=300', 'Lombo de porco, molho agridoce, pimentão', 12, 1, 0, 7),
(27, 44, 'simples', 'Arroz Chau Chau (400g) 🍚', 'Arroz frito com legumes e ovos', 'arroz-chau-chau', 24.90, 21.90, 'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=300', 'Arroz, ervilha, cenoura, ovo, cebolinha', 8, 1, 1, 8),

-- ENTRADAS (subcategoria 37) - 5 produtos
(27, 37, 'simples', 'Rolinho Primavera (4 unidades) 🥟', 'Rolinho primavera de legumes', 'rolinho-primavera-4', 18.90, 15.90, 'https://images.unsplash.com/photo-1569058242252-6df0bf1f03c5?w=300', 'Massa, repolho, cenoura, broto de bambu', 8, 1, 1, 9),
(27, 37, 'simples', 'Rolinho Primavera (8 unidades) 🥟🥟', '8 rolinhos primavera', 'rolinho-primavera-8', 32.90, 28.90, 'https://images.unsplash.com/photo-1569058242252-6df0bf1f03c5?w=300', 'Massa, repolho, cenoura, broto de bambu', 12, 1, 1, 10),
(27, 37, 'simples', 'Guiozá (6 unidades) 🥟', 'Pastel chinês frito', 'guioza-6', 22.90, 19.90, 'https://images.unsplash.com/photo-1569058242252-6df0bf1f03c5?w=300', 'Massa, carne, legumes, shoyu', 10, 1, 1, 11),
(27, 37, 'simples', 'Guiozá (12 unidades) 🥟🥟', '12 pastéis chineses', 'guioza-12', 39.90, 34.90, 'https://images.unsplash.com/photo-1569058242252-6df0bf1f03c5?w=300', 'Massa, carne, legumes, shoyu', 15, 1, 1, 12),
(27, 37, 'simples', 'Tempurá de Legumes (200g) 🥬', 'Legumes empanados', 'tempura-legumes', 24.90, NULL, 'https://images.unsplash.com/photo-1569058242252-6df0bf1f03c5?w=300', 'Cenoura, abobrinha, cebola, massa tempurá', 8, 1, 0, 13),

-- SOPAS (subcategoria 21) - 3 produtos
(27, 21, 'simples', 'Sopa de Wonton (500ml) 🥣', 'Sopa com pasteis chineses', 'sopa-wonton', 28.90, 24.90, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Caldo, wonton de carne, cebolinha', 10, 1, 1, 14),
(27, 21, 'simples', 'Missoshiru (500ml) 🥣', 'Sopa de missô com tofu e algas', 'missoshiru-500', 19.90, NULL, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Missô, tofu, alga wakame, cebolinha', 6, 1, 0, 15),
(27, 21, 'simples', 'Sopa Quente e Azeda (500ml) 🌶️', 'Sopa picante e azeda', 'sopa-quente-azeda', 26.90, NULL, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Caldo, tofu, cogumelos, pimenta, vinagre', 8, 1, 0, 16),

-- BEBIDAS (subcategoria 14) - 4 produtos
(27, 14, 'simples', 'Chá Verde (300ml) 🍵', 'Chá verde natural', 'cha-verde-300', 7.90, NULL, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=300', 'Chá verde', 3, 1, 1, 17),
(27, 14, 'simples', 'Chá de Jasmim (300ml) 🌼', 'Chá de jasmim', 'cha-jasmim-300', 8.90, NULL, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=300', 'Chá de jasmim', 3, 1, 0, 18),
(27, 14, 'simples', 'Suco de Lichia (400ml) 🫒', 'Suco de lichia', 'suco-lichia-400', 12.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Lichia', 3, 1, 1, 19),
(27, 14, 'simples', 'Água de Coco (500ml) 🥥', 'Água de coco', 'agua-coco-500', 9.90, NULL, 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=300', 'Água de coco', 2, 1, 0, 20);



-- =====================================================
-- LOJA 28: Burger Prime (20 produtos - hamburgueria premium)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- HAMBÚRGUERES PREMIUM (subcategoria 12) - 10 produtos
(28, 12, 'personalizavel', 'Prime Burger (180g) 👑', 'Pão brioche, hambúrguer 180g, queijo cheddar, alface, tomate, cebola roxa, maionese da casa', 'prime-burger-180', 32.90, 28.90, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300', 'Pão brioche, hambúrguer 180g, cheddar, alface, tomate, cebola roxa, maionese', 12, 1, 1, 1),
(28, 12, 'personalizavel', 'Prime Burger (250g) 👑', 'Pão brioche, hambúrguer 250g, queijo cheddar, alface, tomate, cebola roxa, maionese da casa', 'prime-burger-250', 39.90, 34.90, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300', 'Pão brioche, hambúrguer 250g, cheddar, alface, tomate, cebola roxa, maionese', 15, 1, 1, 2),
(28, 12, 'personalizavel', 'Bacon Prime Burger 🥓', 'Pão brioche, hambúrguer 180g, queijo, bacon crocante, cebola caramelizada, barbecue', 'bacon-prime', 36.90, 32.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, hambúrguer 180g, queijo prato, bacon, cebola caramelizada, barbecue', 15, 1, 1, 3),
(28, 12, 'personalizavel', 'Cheddar Prime Burger 🧀', 'Pão brioche, hambúrguer 180g, cheddar cremoso, onion rings', 'cheddar-prime', 34.90, NULL, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300', 'Pão brioche, hambúrguer 180g, cheddar cremoso, onion rings', 14, 1, 1, 4),
(28, 12, 'personalizavel', 'Costela Prime Burger 🥩', 'Pão australiano, hambúrguer de costela 180g, queijo, barbecue, cebola crispy', 'costela-prime', 38.90, 34.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão australiano, hambúrguer de costela 180g, queijo, barbecue, cebola crispy', 15, 1, 1, 5),
(28, 12, 'personalizavel', 'Picanha Prime Burger 🥩', 'Pão australiano, hambúrguer de picanha 180g, queijo, maionese temperada, rúcula', 'picanha-prime', 42.90, 37.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão australiano, hambúrguer de picanha 180g, queijo, maionese temperada, rúcula', 15, 1, 1, 6),
(28, 12, 'personalizavel', 'Duplo Prime Burger 🍔🍔', 'Pão brioche, 2 hambúrgueres 150g, 2 queijos, bacon, molho especial', 'duplo-prime', 48.90, 42.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, 2 hambúrgueres 150g, cheddar, prato, bacon, molho especial', 18, 1, 1, 7),
(28, 12, 'personalizavel', 'Cordeiro Prime Burger 🐑', 'Pão brioche, hambúrguer de cordeiro 180g, queijo de cabra, hortelã, rúcula', 'cordeiro-prime', 46.90, NULL, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, hambúrguer de cordeiro 180g, queijo de cabra, hortelã, rúcula', 16, 1, 1, 8),
(28, 12, 'personalizavel', 'Veggie Prime Burger 🌱', 'Pão integral, hambúrguer de grão de bico e quinoa, alface, tomate, rúcula, molho de iogurte', 'veggie-prime', 34.90, NULL, 'https://images.unsplash.com/photo-1525059696034-4967a8e1dca2?w=300', 'Pão integral, hambúrguer de grão de bico e quinoa, alface, tomate, rúcula, molho de iogurte', 12, 1, 0, 9),
(28, 12, 'personalizavel', 'Frango Prime Burger 🐔', 'Pão australiano, filé de frango grelhado, queijo, alface, tomate, maionese de ervas', 'frango-prime', 34.90, 30.90, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300', 'Pão australiano, frango grelhado, queijo, alface, tomate, maionese de ervas', 12, 1, 0, 10),

-- ACOMPANHAMENTOS PREMIUM (subcategoria 13) - 5 produtos
(28, 13, 'simples', 'Batata Rústica (300g) 🍟', 'Batata rústica com alecrim e sal grosso', 'batata-rustica-300', 19.90, 16.90, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=300', 'Batata, alecrim, sal grosso, azeite', 10, 1, 1, 11),
(28, 13, 'simples', 'Batata com Cheddar e Bacon (300g) 🧀🥓', 'Batata frita com cheddar e bacon', 'batata-cheddar-bacon-300', 26.90, 23.90, 'https://images.unsplash.com/photo-1585109649138-45c85e3e0468?w=300', 'Batata, cheddar, bacon', 12, 1, 1, 12),
(28, 13, 'simples', 'Onion Rings (8 unidades) 🧅', 'Anéis de cebola empanados crocantes', 'onion-rings-8', 19.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Cebola, farinha panko, ovos', 10, 1, 1, 13),
(28, 13, 'simples', 'Anéis de Cebola com Molho (8 unidades) 🧅', 'Onion rings com molho barbecue', 'onion-rings-molho-8', 22.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Cebola, farinha panko, molho barbecue', 10, 1, 0, 14),
(28, 13, 'simples', 'Polenta Frita com Parmesão (250g) 🌽', 'Polenta frita com parmesão', 'polenta-parmesao-250', 21.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Polenta, parmesão, sal', 10, 1, 0, 15),

-- MOLHES ESPECIAIS (subcategoria 15) - 3 produtos
(28, 15, 'simples', 'Molho da Casa (50ml) 🏠', 'Molho especial da casa', 'molho-casa-50', 4.90, NULL, 'https://images.unsplash.com/photo-1581262177161-977bcc9b86b7?w=300', 'Maionese, ervas, especiarias', 2, 1, 1, 16),
(28, 15, 'simples', 'Molho Barbecue (50ml) 🍖', 'Molho barbecue defumado', 'molho-barbecue-50', 4.90, NULL, 'https://images.unsplash.com/photo-1581262177161-977bcc9b86b7?w=300', 'Barbecue defumado', 2, 1, 1, 17),
(28, 15, 'simples', 'Molho de Pimenta (50ml) 🌶️', 'Molho de pimenta artesanal', 'molho-pimenta-50', 4.90, NULL, 'https://images.unsplash.com/photo-1581262177161-977bcc9b86b7?w=300', 'Pimenta, especiarias', 2, 1, 0, 18),

-- BEBIDAS (subcategoria 14) - 2 produtos
(28, 14, 'simples', 'Milkshake de Chocolate (500ml) 🥤', 'Milkshake cremoso de chocolate', 'milkshake-chocolate-500', 22.90, 19.90, 'https://images.unsplash.com/photo-1572490122744-4ab00e582f71?w=300', 'Sorvete, leite, calda de chocolate', 5, 1, 1, 19),
(28, 14, 'simples', 'Milkshake de Morango (500ml) 🍓', 'Milkshake cremoso de morango', 'milkshake-morango-500', 22.90, NULL, 'https://images.unsplash.com/photo-1572490122744-4ab00e582f71?w=300', 'Sorvete, leite, calda de morango', 5, 1, 0, 20);


-- =====================================================
-- LOJA 29: Tempero Nordestino (20 produtos - culinária nordestina)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PRATOS PRINCIPAIS (subcategoria 45) - 8 produtos
(29, 45, 'simples', 'Baião de Dois (individual) 🍛', 'Arroz, feijão de corda, carne de sol, queijo coalho', 'baiao-dois-individual', 34.90, 29.90, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Arroz, feijão de corda, carne de sol, queijo coalho, manteiga de garrafa', 15, 1, 1, 1),
(29, 45, 'simples', 'Baião de Dois (família) 👪', 'Baião de dois para 3 pessoas', 'baiao-dois-familia', 79.90, 69.90, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Arroz, feijão de corda, carne de sol, queijo coalho, manteiga de garrafa', 25, 1, 1, 2),
(29, 45, 'simples', 'Carne de Sol com Mandioca (individual) 🥩', 'Carne de sol acebolada com mandioca cozida', 'carne-sol-mandioca-individual', 42.90, 37.90, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Carne de sol, mandioca, cebola, manteiga de garrafa', 15, 1, 1, 3),
(29, 45, 'simples', 'Carne de Sol com Mandioca (família) 👪', 'Carne de sol para 3 pessoas', 'carne-sol-mandioca-familia', 99.90, 89.90, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Carne de sol, mandioca, cebola, manteiga de garrafa', 25, 1, 1, 4),
(29, 45, 'simples', 'Rubacão (individual) 🍛', 'Arroz, feijão verde, carne de sol, queijo coalho', 'rubacao-individual', 36.90, NULL, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Arroz, feijão verde, carne de sol, queijo coalho', 15, 1, 0, 5),
(29, 45, 'simples', 'Panelada (individual) 🍲', 'Comida típica com miúdos de porco', 'panelada-individual', 38.90, NULL, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Miúdos de porco, feijão, legumes', 20, 1, 0, 6),
(29, 45, 'simples', 'Buchada de Bode (individual) 🐐', 'Buchada de bode típica', 'buchada-individual', 44.90, 39.90, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Miúdos de bode, temperos nordestinos', 25, 1, 1, 7),
(29, 45, 'simples', 'Galinha Caipira (individual) 🐔', 'Galinha caipira cozida com legumes', 'galinha-caipira-individual', 38.90, NULL, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Galinha caipira, legumes, temperos', 20, 1, 0, 8),

-- TAPIOCAS (subcategoria 46) - 5 produtos
(29, 46, 'simples', 'Tapioca de Carne de Sol com Queijo 🥩', 'Tapioca recheada com carne de sol e queijo coalho', 'tapioca-carne-sol-queijo', 24.90, 21.90, 'https://images.unsplash.com/photo-1554401929-ef80c7c400ee?w=300', 'Goma de tapioca, carne de sol, queijo coalho', 6, 1, 1, 9),
(29, 46, 'simples', 'Tapioca de Frango com Catupiry 🐔', 'Tapioca recheada com frango e catupiry', 'tapioca-frango-catupiry', 22.90, NULL, 'https://images.unsplash.com/photo-1554401929-ef80c7c400ee?w=300', 'Goma de tapioca, frango, catupiry', 6, 1, 1, 10),
(29, 46, 'simples', 'Tapioca de Queijo Coalho 🧀', 'Tapioca recheada com queijo coalho', 'tapioca-queijo-coalho', 18.90, NULL, 'https://images.unsplash.com/photo-1554401929-ef80c7c400ee?w=300', 'Goma de tapioca, queijo coalho', 5, 1, 0, 11),
(29, 46, 'simples', 'Tapioca de Cocada 🥥', 'Tapioca doce com cocada', 'tapioca-cocada', 19.90, NULL, 'https://images.unsplash.com/photo-1554401929-ef80c7c400ee?w=300', 'Goma de tapioca, cocada, leite condensado', 5, 1, 0, 12),
(29, 46, 'simples', 'Tapioca Mista (carne de sol + queijo + frango) 🥩🧀🐔', 'Tapioca com recheios variados', 'tapioca-mista', 28.90, 24.90, 'https://images.unsplash.com/photo-1554401929-ef80c7c400ee?w=300', 'Goma de tapioca, carne de sol, queijo coalho, frango', 8, 1, 1, 13),

-- CARNES DE SOL (subcategoria 45) - 3 produtos
(29, 45, 'simples', 'Porção de Carne de Sol (300g) 🥩', 'Carne de sol acebolada', 'porcao-carne-sol-300', 48.90, 42.90, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Carne de sol, cebola, manteiga de garrafa', 12, 1, 1, 14),
(29, 45, 'simples', 'Porção de Carne de Sol (500g) 🥩', 'Carne de sol acebolada', 'porcao-carne-sol-500', 74.90, 64.90, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Carne de sol, cebola, manteiga de garrafa', 15, 1, 1, 15),
(29, 45, 'simples', 'Porção de Queijo Coalho (200g) 🧀', 'Queijo coalho grelhado', 'porcao-queijo-coalho-200', 24.90, NULL, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300', 'Queijo coalho', 6, 1, 1, 16),

-- BEBIDAS TÍPICAS (subcategoria 14) - 4 produtos
(29, 14, 'simples', 'Suco de Caju (500ml) 🧃', 'Suco de caju natural', 'suco-caju-500', 12.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Caju', 3, 1, 1, 17),
(29, 14, 'simples', 'Suco de Acerola (500ml) 🍒', 'Suco de acerola natural', 'suco-acerola-500', 12.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Acerola', 3, 1, 0, 18),
(29, 14, 'simples', 'Suco de Mangaba (500ml) 🥭', 'Suco de mangaba', 'suco-mangaba-500', 14.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Mangaba', 3, 1, 0, 19),
(29, 14, 'simples', 'Água de Coco (500ml) 🥥', 'Água de coco gelada', 'agua-coco-500', 8.90, NULL, 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=300', 'Água de coco', 2, 1, 1, 20);


-- =====================================================
-- LOJA 30: Donatella (20 produtos - doces finos e sobremesas)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- BOLOS E TORTAS (subcategoria 26) - 8 produtos
(30, 26, 'simples', 'Torta de Limão (fatia) 🍋', 'Torta de limão com merengue', 'torta-limao-fatia', 14.90, 12.90, 'https://images.unsplash.com/photo-1519915028121-7d3463d20b13?w=300', 'Massa, creme de limão, merengue', 5, 1, 1, 1),
(30, 26, 'simples', 'Torta de Limão (inteira) 🍋🎂', 'Torta de limão inteira (10 fatias)', 'torta-limao-inteira', 129.90, 109.90, 'https://images.unsplash.com/photo-1519915028121-7d3463d20b13?w=300', 'Massa, creme de limão, merengue', 40, 1, 1, 2),
(30, 26, 'simples', 'Cheesecake de Frutas Vermelhas (fatia) 🍓', 'Cheesecake com calda de frutas vermelhas', 'cheesecake-frutas-fatia', 16.90, 14.90, 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=300', 'Queijo cream, frutas vermelhas', 5, 1, 1, 3),
(30, 26, 'simples', 'Cheesecake de Frutas Vermelhas (inteiro) 🍓🎂', 'Cheesecake inteiro', 'cheesecake-frutas-inteiro', 149.90, 129.90, 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=300', 'Queijo cream, frutas vermelhas', 45, 1, 1, 4),
(30, 26, 'simples', 'Bolo de Chocolate com Recheio (fatia) 🍫', 'Bolo de chocolate com recheio de brigadeiro', 'bolo-chocolate-fatia', 14.90, NULL, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Chocolate, brigadeiro, massa', 5, 1, 1, 5),
(30, 26, 'simples', 'Bolo de Chocolate com Recheio (inteiro) 🍫🎂', 'Bolo inteiro (10 fatias)', 'bolo-chocolate-inteiro', 139.90, 119.90, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Chocolate, brigadeiro, massa', 45, 1, 1, 6),
(30, 26, 'simples', 'Torta de Maçã (fatia) 🍎', 'Torta de maçã com canela', 'torta-maca-fatia', 13.90, NULL, 'https://images.unsplash.com/photo-1568571780763-927d2553678c?w=300', 'Maçã, canela, massa folhada', 5, 1, 0, 7),
(30, 26, 'simples', 'Torta de Maçã (inteira) 🍎🎂', 'Torta de maçã inteira', 'torta-maca-inteira', 119.90, NULL, 'https://images.unsplash.com/photo-1568571780763-927d2553678c?w=300', 'Maçã, canela, massa folhada', 40, 1, 0, 8),

-- DOCES FINOS (subcategoria 26) - 6 produtos
(30, 26, 'simples', 'Macaron Francês (4 unidades) 🇫🇷', 'Macarons sortidos', 'macaron-4', 24.90, 21.90, 'https://images.unsplash.com/photo-1569864358642-9d1684040f43?w=300', 'Amêndoas, recheio variado', 10, 1, 1, 9),
(30, 26, 'simples', 'Macaron Francês (12 unidades) 🇫🇷', 'Caixa com 12 macarons sortidos', 'macaron-12', 69.90, 59.90, 'https://images.unsplash.com/photo-1569864358642-9d1684040f43?w=300', 'Amêndoas, recheio variado', 20, 1, 1, 10),
(30, 26, 'simples', 'Financier de Amêndoas (4 unidades) 🍰', 'Bolinhos franceses de amêndoas', 'financier-4', 22.90, NULL, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Amêndoas, manteiga, açúcar', 8, 1, 1, 11),
(30, 26, 'simples', 'Creme Brûlée (individual) 🇫🇷', 'Creme brûlée com crosta de açúcar queimado', 'creme-brulee', 19.90, 16.90, 'https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=300', 'Creme, baunilha, açúcar', 10, 1, 1, 12),
(30, 26, 'simples', 'Petit Gateau (individual) 🍰', 'Bolo de chocolate com centro cremoso', 'petit-gateau', 18.90, 16.90, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Chocolate, manteiga, farinha, ovo', 8, 1, 1, 13),
(30, 26, 'simples', 'Petit Gateau com Sorvete 🍰🍦', 'Petit gateau com sorvete de creme', 'petit-gateau-sorvete', 24.90, 21.90, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Chocolate, sorvete de creme', 8, 1, 1, 14),

-- SOBREMAS GELADAS (subcategoria 32) - 4 produtos
(30, 32, 'simples', 'Profiteroles (4 unidades) 🥐', 'Massas folhadas recheadas com sorvete e calda de chocolate', 'profiteroles-4', 26.90, 23.90, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Massa folhada, sorvete, calda de chocolate', 8, 1, 1, 15),
(30, 32, 'simples', 'Tiramisu (individual) 🇮🇹', 'Sobremesa italiana de café', 'tiramisu-individual', 18.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Mascarpone, café, biscoito champagne', 5, 1, 1, 16),
(30, 32, 'simples', 'Panna Cotta (individual) 🇮🇹', 'Creme italiano com calda de frutas vermelhas', 'panna-cotta', 17.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Creme, frutas vermelhas', 5, 1, 0, 17),
(30, 32, 'simples', 'Sorvete Artesanal (2 bolas) 🍦', 'Duas bolas de sorvete à escolha', 'sorvete-2-bolas', 16.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Sorvete artesanal', 3, 1, 0, 18),

-- BEBIDAS QUENTES (subcategoria 14) - 2 produtos
(30, 14, 'simples', 'Café Gourmet (100ml) ☕', 'Café especial', 'cafe-gourmet-100', 6.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café especial', 3, 1, 1, 19),
(30, 14, 'simples', 'Cappuccino Italiano (250ml) 🇮🇹', 'Cappuccino cremoso', 'cappuccino-italiano-250', 12.90, 10.90, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite, chocolate italiano', 5, 1, 1, 20);


-- =====================================================
-- LOJA 31: Comida Árabe (20 produtos - culinária árabe)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PRATOS PRINCIPAIS (subcategoria 47) - 8 produtos
(31, 47, 'simples', 'Kafta (4 unidades) 🥩', 'Kafta de carne moída com temperos árabes', 'kafta-4', 28.90, 24.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Carne moída, cebola, salsinha, hortelã, especiarias', 12, 1, 1, 1),
(31, 47, 'simples', 'Kafta (8 unidades) 🥩🥩', '8 kaftas', 'kafta-8', 49.90, 44.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Carne moída, cebola, salsinha, hortelã, especiarias', 18, 1, 1, 2),
(31, 47, 'simples', 'Esfiha de Carne (4 unidades) 🥟', 'Esfihas abertas de carne', 'esfiha-carne-4', 24.90, 21.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Massa, carne moída, cebola, tomate', 10, 1, 1, 3),
(31, 47, 'simples', 'Esfiha de Carne (8 unidades) 🥟🥟', '8 esfihas de carne', 'esfiha-carne-8', 44.90, 39.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Massa, carne moída, cebola, tomate', 15, 1, 1, 4),
(31, 47, 'simples', 'Esfiha de Queijo (4 unidades) 🧀', 'Esfihas de queijo', 'esfiha-queijo-4', 24.90, NULL, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Massa, queijo mussarela, orégano', 10, 1, 1, 5),
(31, 47, 'simples', 'Esfiha de Queijo (8 unidades) 🧀🧀', '8 esfihas de queijo', 'esfiha-queijo-8', 44.90, NULL, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Massa, queijo mussarela, orégano', 15, 1, 0, 6),
(31, 47, 'simples', 'Quibe (4 unidades) 🥩', 'Quibe frito', 'quibe-4', 26.90, 23.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Trigo para quibe, carne moída, hortelã, cebola', 12, 1, 1, 7),
(31, 47, 'simples', 'Quibe (8 unidades) 🥩🥩', '8 quibes', 'quibe-8', 46.90, 41.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Trigo para quibe, carne moída, hortelã, cebola', 18, 1, 1, 8),

-- ENTRADAS E PASTAS (subcategoria 48) - 6 produtos
(31, 48, 'simples', 'Homus (300g) 🫘', 'Pasta de grão de bico com tahine', 'homus-300', 22.90, 19.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Grão de bico, tahine, limão, alho, azeite', 5, 1, 1, 9),
(31, 48, 'simples', 'Babaganuche (300g) 🍆', 'Pasta de berinjela defumada', 'babaganuche-300', 24.90, NULL, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Berinjela, tahine, limão, alho, azeite', 5, 1, 1, 10),
(31, 48, 'simples', 'Kibe Cru (200g) 🥩', 'Kibe cru temperado', 'kibe-cru-200', 28.90, 24.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Carne moída crua, trigo, hortelã, cebola', 5, 1, 1, 11),
(31, 48, 'simples', 'Tabule (300g) 🥗', 'Salada de trigo com hortelã e salsinha', 'tabule-300', 21.90, NULL, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Trigo, salsinha, hortelã, tomate, cebola, limão', 5, 1, 1, 12),
(31, 48, 'simples', 'Coalhada Seca (200g) 🥛', 'Coalhada escorrida', 'coalhada-seca-200', 18.90, NULL, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Iogurte coalhado', 3, 1, 0, 13),
(31, 48, 'simples', 'Pão Sírio (4 unidades) 🫓', 'Pão sírio tradicional', 'pao-sirio-4', 9.90, NULL, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Farinha, fermento, água', 5, 1, 1, 14),

-- COMBINADOS (subcategoria 47) - 3 produtos
(31, 47, 'combo', 'Combo Árabe (2 pessoas) 👥', '4 quibes, 4 kaftas, homus, pão sírio', 'combo-arabe-2', 69.90, 59.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Quibe, kafta, homus, pão sírio', 20, 1, 1, 15),
(31, 47, 'combo', 'Combo Árabe (4 pessoas) 👨‍👩‍👧‍👦', '8 quibes, 8 kaftas, homus, babaganuche, coalhada, pão sírio', 'combo-arabe-4', 129.90, 109.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Quibe, kafta, homus, babaganuche, coalhada, pão sírio', 30, 1, 1, 16),
(31, 47, 'combo', 'Combo Esfihas (12 unidades) 🥟', '6 esfihas de carne, 6 de queijo', 'combo-esfihas-12', 59.90, 52.90, 'https://images.unsplash.com/photo-1606756790138-261d2b21cd75?w=300', 'Esfihas de carne e queijo', 20, 1, 1, 17),

-- BEBIDAS (subcategoria 14) - 3 produtos
(31, 14, 'simples', 'Suco de Romã (500ml) ❤️', 'Suco de romã natural', 'suco-roma-500', 16.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Romã', 3, 1, 1, 18),
(31, 14, 'simples', 'Chá de Hortelã (300ml) 🍃', 'Chá de hortelã fresco', 'cha-hortela-300', 7.90, NULL, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=300', 'Hortelã, açúcar', 3, 1, 0, 19),
(31, 14, 'simples', 'Suco de Uva (500ml) 🍇', 'Suco de uva integral', 'suco-uva-500', 12.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Uva', 3, 1, 0, 20);


-- =====================================================
-- LOJA 32: Empório do Grão (20 produtos - cafés especiais e brunch)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- CAFÉS ESPECIAIS (subcategoria 23) - 8 produtos
(32, 23, 'simples', 'Café Expresso (60ml) ☕', 'Café expresso encorpado', 'cafe-expresso-60', 6.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café especial', 2, 1, 1, 1),
(32, 23, 'simples', 'Café Americano (200ml) 🇺🇸', 'Café americano suave', 'cafe-americano-200', 8.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café especial, água', 3, 1, 0, 2),
(32, 23, 'simples', 'Cappuccino Cremoso (300ml) 🥤', 'Cappuccino com espuma cremosa', 'cappuccino-300', 14.90, 12.90, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite vaporizado, espuma, chocolate', 5, 1, 1, 3),
(32, 23, 'simples', 'Latte Macchiato (350ml) 🥛', 'Leite vaporizado com café', 'latte-350', 15.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Leite vaporizado, café expresso', 5, 1, 1, 4),
(32, 23, 'simples', 'Mocha com Chocolate Belga (350ml) 🍫', 'Café com leite e chocolate belga', 'mocha-chocolate-350', 17.90, 15.90, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite, chocolate belga, chantilly', 6, 1, 1, 5),
(32, 23, 'simples', 'Caramelo Macchiato (350ml) 🍯', 'Café com leite e calda de caramelo', 'caramelo-macchiato-350', 18.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite, calda de caramelo, chantilly', 6, 1, 1, 6),
(32, 23, 'simples', 'Café Coado (300ml) 🫗', 'Café coado da casa', 'cafe-coado-300', 9.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café especial coado', 4, 1, 0, 7),
(32, 23, 'simples', 'Mocha Gelado (400ml) 🧊', 'Mocha batido com gelo', 'mocha-gelado-400', 19.90, 17.90, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café, chocolate, leite, gelo, chantilly', 5, 1, 1, 8),

-- CAFÉS DA MANHÃ (subcategoria 49) - 5 produtos
(32, 49, 'combo', 'Café da Manhã Simples 🥐', 'Café, pão de queijo, mini croissant', 'cafe-manha-simples', 26.90, 22.90, 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=300', 'Café, pão de queijo, croissant', 8, 1, 1, 9),
(32, 49, 'combo', 'Café da Manhã Completo 🍳', 'Café, suco de laranja, pão de queijo, croissant, iogurte com granola', 'cafe-manha-completo', 39.90, 34.90, 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=300', 'Café, suco, pão de queijo, croissant, iogurte, granola', 10, 1, 1, 10),
(32, 49, 'combo', 'Combo Croissant + Cappuccino 🥐', 'Croissant de chocolate e cappuccino', 'combo-croissant-cappuccino', 24.90, 21.90, 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=300', 'Croissant de chocolate, cappuccino', 5, 1, 1, 11),
(32, 49, 'combo', 'Combo Pão de Queijo + Café ☕', '4 pães de queijo e café', 'combo-pao-queijo-cafe', 19.90, 17.90, 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=300', 'Pão de queijo, café', 5, 1, 1, 12),
(32, 49, 'simples', 'Iogurte com Granola e Frutas (300g) 🥣', 'Iogurte natural com granola e frutas', 'iogurte-granola-300', 18.90, NULL, 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=300', 'Iogurte, granola, morango, banana', 3, 1, 0, 13),

-- SALGADOS E LANCHES (subcategoria 25) - 4 produtos
(32, 25, 'simples', 'Pão de Queijo (4 unidades) 🧀', 'Pão de queijo tradicional', 'pao-queijo-4', 12.90, NULL, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Polvilho, queijo, ovos', 5, 1, 1, 14),
(32, 25, 'simples', 'Pão de Queijo Recheado com Catupiry (4 unidades) 🧀', 'Pão de queijo recheado', 'pao-queijo-catupiry-4', 18.90, 16.90, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Polvilho, queijo, catupiry', 7, 1, 1, 15),
(32, 25, 'simples', 'Croissant de Presunto e Queijo 🥐', 'Croissant folhado recheado', 'croissant-presunto-queijo', 14.90, NULL, 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=300', 'Croissant, presunto, queijo', 5, 1, 1, 16),
(32, 25, 'simples', 'Torta Salgada (fatia) 🥧', 'Torta de frango com catupiry', 'torta-salgada-fatia', 16.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Frango, catupiry, massa podre', 5, 1, 0, 17),

-- BEBIDAS QUENTES (subcategoria 14) - 3 produtos
(32, 14, 'simples', 'Chocolate Quente (300ml) 🍫', 'Chocolate quente cremoso', 'chocolate-quente-300', 13.90, 11.90, 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=300', 'Leite, chocolate, chantilly', 5, 1, 1, 18),
(32, 14, 'simples', 'Chai Latte (300ml) 🫖', 'Chá indiano com leite e especiarias', 'chai-latte-300', 15.90, NULL, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=300', 'Chá preto, leite, especiarias, mel', 6, 1, 0, 19),
(32, 14, 'simples', 'Matcha Latte (300ml) 🍵', 'Chá matcha com leite vaporizado', 'matcha-latte-300', 16.90, NULL, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=300', 'Matcha, leite vaporizado', 5, 1, 1, 20);


-- =====================================================
-- LOJA 33: Churrascada (20 produtos - churrasco brasileiro)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- CARNES (subcategoria 50) - 10 produtos
(33, 50, 'simples', 'Picanha (300g) 🥩', 'Picanha grelhada no ponto', 'picanha-300', 59.90, 52.90, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Picanha, sal grosso', 20, 1, 1, 1),
(33, 50, 'simples', 'Picanha (500g) 🥩🥩', 'Picanha para duas pessoas', 'picanha-500', 94.90, 84.90, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Picanha, sal grosso', 25, 1, 1, 2),
(33, 50, 'simples', 'Maminha (300g) 🥩', 'Maminha macia', 'maminha-300', 49.90, NULL, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Maminha, sal grosso', 20, 1, 1, 3),
(33, 50, 'simples', 'Contrafilé (300g) 🥩', 'Contrafilé suculento', 'contrafile-300', 46.90, NULL, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Contrafilé, sal grosso', 20, 1, 0, 4),
(33, 50, 'simples', 'Alcatra (300g) 🥩', 'Alcatra fatiada', 'alcatra-300', 48.90, NULL, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Alcatra, sal grosso', 20, 1, 0, 5),
(33, 50, 'simples', 'Fraldinha (300g) 🥩', 'Fraldinha saborosa', 'fraldinha-300', 44.90, 39.90, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Fraldinha, sal grosso', 18, 1, 1, 6),
(33, 50, 'simples', 'Costela (500g) 🍖', 'Costela assada lentamente', 'costela-500', 59.90, 52.90, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Costela, sal grosso', 30, 1, 1, 7),
(33, 50, 'simples', 'Linguiça Toscana (300g) 🌭', 'Linguiça toscana grelhada', 'linguica-300', 34.90, 29.90, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Linguiça toscana', 15, 1, 1, 8),
(33, 50, 'simples', 'Coração de Frango (200g) ❤️', 'Coração de frango grelhado', 'coracao-200', 32.90, NULL, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Coração de frango', 15, 1, 1, 9),
(33, 50, 'simples', 'Asinha de Frango (300g) 🍗', 'Asinha de frango crocante', 'asinha-300', 36.90, NULL, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Asinha de frango', 18, 1, 0, 10),

-- ACOMPANHAMENTOS (subcategoria 51) - 5 produtos
(33, 51, 'simples', 'Arroz Carreteiro (400g) 🍚', 'Arroz com carne e temperos', 'arroz-carreteiro-400', 28.90, 24.90, 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=300', 'Arroz, carne, bacon, linguiça, temperos', 15, 1, 1, 11),
(33, 51, 'simples', 'Farofa de Bacon (250g) 🥓', 'Farofa crocante com bacon', 'farofa-bacon-250', 19.90, 16.90, 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=300', 'Farinha, bacon, cebola, ovos', 8, 1, 1, 12),
(33, 51, 'simples', 'Vinagrete (300g) 🥗', 'Vinagrete tradicional', 'vinagrete-300', 14.90, NULL, 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=300', 'Tomate, cebola, pimentão, vinagre, azeite', 5, 1, 1, 13),
(33, 51, 'simples', 'Mandioca Frita (300g) 🥔', 'Mandioca frita crocante', 'mandioca-frita-300', 21.90, NULL, 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=300', 'Mandioca', 12, 1, 0, 14),
(33, 51, 'simples', 'Polenta Frita (300g) 🌽', 'Polenta frita', 'polenta-frita-300', 21.90, NULL, 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=300', 'Polenta', 12, 1, 0, 15),

-- COMBINADOS (subcategoria 50) - 3 produtos
(33, 50, 'combo', 'Combo Churrasco (2 pessoas) 👥', 'Picanha 500g, linguiça, arroz carreteiro, farofa, vinagrete', 'combo-churrasco-2', 149.90, 129.90, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Picanha, linguiça, arroz, farofa, vinagrete', 35, 1, 1, 16),
(33, 50, 'combo', 'Combo Churrasco (4 pessoas) 👨‍👩‍👧‍👦', 'Picanha 1kg, maminha 500g, linguiça, coração, arroz, farofa, vinagrete, mandioca', 'combo-churrasco-4', 279.90, 249.90, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Picanha, maminha, linguiça, coração, arroz, farofa, vinagrete, mandioca', 50, 1, 1, 17),
(33, 50, 'combo', 'Combo Costela (2 pessoas) 🍖', 'Costela 1kg, arroz carreteiro, farofa', 'combo-costela-2', 129.90, 114.90, 'https://images.unsplash.com/photo-1558030006-450675393462?w=300', 'Costela, arroz carreteiro, farofa', 40, 1, 1, 18),

-- BEBIDAS (subcategoria 14) - 2 produtos
(33, 14, 'simples', 'Caipirinha de Limão (500ml) 🍋', 'Caipirinha tradicional', 'caipirinha-500', 24.90, 21.90, 'https://images.unsplash.com/photo-1513558161293-e3d4df538c9b?w=300', 'Limão, cachaça, açúcar, gelo', 5, 1, 1, 19),
(33, 14, 'simples', 'Cerveja Long Neck (355ml) 🍺', 'Cerveja gelada', 'cerveja-355', 9.90, NULL, 'https://images.unsplash.com/photo-1586994496097-9c0fb7a509ac?w=300', 'Cerveja', 2, 1, 1, 20);


-- =====================================================
-- LOJA 34: Comida Mexicana (20 produtos - culinária mexicana)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PRATOS PRINCIPAIS (subcategoria 52) - 8 produtos
(34, 52, 'simples', 'Burrito de Carne (grande) 🌯', 'Tortilla grande recheada com carne, arroz, feijão, queijo e guacamole', 'burrito-carne', 34.90, 29.90, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=300', 'Tortilla, carne moída, arroz, feijão, queijo, guacamole, sour cream', 12, 1, 1, 1),
(34, 52, 'simples', 'Burrito de Frango (grande) 🌯', 'Tortilla grande recheada com frango, arroz, feijão, queijo e guacamole', 'burrito-frango', 32.90, 28.90, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=300', 'Tortilla, frango, arroz, feijão, queijo, guacamole, sour cream', 12, 1, 1, 2),
(34, 52, 'simples', 'Burrito Vegano (grande) 🌱', 'Tortilla com legumes, arroz, feijão preto, guacamole', 'burrito-vegano', 29.90, NULL, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=300', 'Tortilla, legumes, arroz, feijão preto, guacamole, pico de gallo', 12, 1, 0, 3),
(34, 52, 'simples', 'Quesadilla de Carne (4 fatias) 🧀', 'Tortilla grelhada com queijo e carne', 'quesadilla-carne-4', 28.90, 24.90, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e0?w=300', 'Tortilla, queijo, carne moída, pimentão', 8, 1, 1, 4),
(34, 52, 'simples', 'Quesadilla de Frango (4 fatias) 🧀', 'Tortilla grelhada com queijo e frango', 'quesadilla-frango-4', 26.90, NULL, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e0?w=300', 'Tortilla, queijo, frango', 8, 1, 0, 5),
(34, 52, 'simples', 'Tacos (3 unidades) 🌮', 'Tacos crocantes com carne, alface, tomate, queijo e creme azedo', 'tacos-3', 29.90, 25.90, 'https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=300', 'Tortilha crocante, carne, alface, tomate, queijo, sour cream', 10, 1, 1, 6),
(34, 52, 'simples', 'Nachos com Carne (grande) 🧀', 'Nachos cobertos com carne, queijo derretido, guacamole, sour cream e jalapeño', 'nachos-carne', 38.90, 32.90, 'https://images.unsplash.com/photo-1513456852971-30c0b8199d4d?w=300', 'Nachos, carne moída, queijo cheddar, guacamole, sour cream, jalapeño', 10, 1, 1, 7),
(34, 52, 'simples', 'Fajitas de Carne (300g) 🥩', 'Tiras de carne com pimentões e cebola, servidas com tortillas', 'fajitas-carne-300', 42.90, 37.90, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=300', 'Carne, pimentão, cebola, tortillas', 15, 1, 1, 8),

-- ENTRADAS (subcategoria 37) - 5 produtos
(34, 37, 'simples', 'Guacamole (250g) 🥑', 'Guacamole fresco', 'guacamole-250', 22.90, 19.90, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e0?w=300', 'Abacate, tomate, cebola, coentro, limão', 5, 1, 1, 9),
(34, 37, 'simples', 'Guacamole com Nachos 🥑', 'Guacamole servido com nachos', 'guacamole-nachos', 28.90, 24.90, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e0?w=300', 'Abacate, tomate, cebola, coentro, limão, nachos', 5, 1, 1, 10),
(34, 37, 'simples', 'Sour Cream (150g) 🥛', 'Creme azedo', 'sour-cream-150', 10.90, NULL, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e0?w=300', 'Creme de leite, limão, sal', 3, 1, 0, 11),
(34, 37, 'simples', 'Pico de Gallo (200g) 🍅', 'Salada de tomate, cebola e coentro', 'pico-gallo-200', 12.90, NULL, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e0?w=300', 'Tomate, cebola, coentro, limão', 5, 1, 0, 12),
(34, 37, 'simples', 'Jalapeños (50g) 🌶️', 'Pimenta jalapeño em conserva', 'jalapenos-50', 6.90, NULL, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e0?w=300', 'Jalapeño', 2, 1, 1, 13),

-- COMBINADOS (subcategoria 52) - 4 produtos
(34, 52, 'combo', 'Combo Mexicano (2 pessoas) 👥', '2 burritos, nachos com queijo, guacamole', 'combo-mexicano-2', 89.90, 79.90, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=300', 'Burritos, nachos, guacamole', 20, 1, 1, 14),
(34, 52, 'combo', 'Combo Mexicano (4 pessoas) 👨‍👩‍👧‍👦', '4 burritos, nachos supremos, guacamole, sour cream, jalapeños', 'combo-mexicano-4', 169.90, 149.90, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=300', 'Burritos, nachos, guacamole, sour cream, jalapeños', 30, 1, 1, 15),
(34, 52, 'combo', 'Combo Tacos (6 unidades) 🌮', '6 tacos variados', 'combo-tacos-6', 54.90, 48.90, 'https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=300', 'Tacos de carne e frango', 15, 1, 1, 16),
(34, 52, 'combo', 'Combo Quesadillas (8 fatias) 🧀', '8 fatias de quesadilla mistas', 'combo-quesadillas-8', 49.90, 44.90, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e0?w=300', 'Quesadillas de carne e frango', 15, 1, 1, 17),

-- BEBIDAS (subcategoria 14) - 3 produtos
(34, 14, 'simples', 'Margarita (500ml) 🍹', 'Margarita tradicional com tequila', 'margarita-500', 32.90, 28.90, 'https://images.unsplash.com/photo-1513558161293-e3d4df538c9b?w=300', 'Tequila, limão, licor de laranja, gelo', 5, 1, 1, 18),
(34, 14, 'simples', 'Suco de Limão (500ml) 🍋', 'Suco de limão fresco', 'suco-limao-500', 12.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Limão', 3, 1, 0, 19),
(34, 14, 'simples', 'Soda Mexicana (355ml) 🥤', 'Refrigerante de garrafa', 'soda-mexicana-355', 8.90, NULL, 'https://images.unsplash.com/photo-1581006852262-4305c2fad871?w=300', 'Refrigerante', 2, 1, 0, 20);


-- =====================================================
-- LOJA 35: Adega & Petiscos (20 produtos - bar e petiscaria)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PETISCOS (subcategoria 53) - 8 produtos
(35, 53, 'simples', 'Batata Frita (500g) 🍟', 'Batata frita crocante', 'batata-frita-500', 24.90, 21.90, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=300', 'Batata, sal', 10, 1, 1, 1),
(35, 53, 'simples', 'Batata com Cheddar e Bacon (500g) 🧀🥓', 'Batata frita com cheddar e bacon', 'batata-cheddar-bacon-500', 34.90, 29.90, 'https://images.unsplash.com/photo-1585109649138-45c85e3e0468?w=300', 'Batata, cheddar, bacon', 12, 1, 1, 2),
(35, 53, 'simples', 'Isca de Frango (400g) 🐔', 'Iscas de frango empanadas', 'isca-frango-400', 32.90, 28.90, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Frango empanado', 12, 1, 1, 3),
(35, 53, 'simples', 'Calabresa Acebolada (400g) 🌭', 'Calabresa frita com cebola', 'calabresa-acebolada-400', 34.90, 30.90, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Calabresa, cebola', 12, 1, 1, 4),
(35, 53, 'simples', 'Frango a Passarinho (500g) 🐔', 'Frango frito temperado', 'frango-passarinho-500', 38.90, 33.90, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Frango, alho, limão', 15, 1, 1, 5),
(35, 53, 'simples', 'Bolinhos de Bacalhau (6 unidades) 🐟', 'Bolinhos de bacalhau', 'bolinho-bacalhau-6', 42.90, 37.90, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Bacalhau, batata, salsinha', 15, 1, 1, 6),
(35, 53, 'simples', 'Pastel de Carne (6 unidades) 🥟', 'Pastéis de carne', 'pastel-carne-6', 28.90, 24.90, 'https://images.unsplash.com/photo-1604467715878-83e57e8bc129?w=300', 'Massa, carne moída', 12, 1, 1, 7),
(35, 53, 'simples', 'Coxinha de Frango (6 unidades) 🥟', 'Coxinhas de frango com catupiry', 'coxinha-6', 32.90, 28.90, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Frango, catupiry, massa', 12, 1, 1, 8),

-- PORÇÕES (subcategoria 53) - 5 produtos
(35, 53, 'simples', 'Porção de Queijo Coalho (400g) 🧀', 'Queijo coalho grelhado', 'queijo-coalho-400', 36.90, 32.90, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Queijo coalho', 10, 1, 1, 9),
(35, 53, 'simples', 'Porção de Mandioca Frita (500g) 🥔', 'Mandioca frita', 'mandioca-frita-500', 28.90, NULL, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Mandioca', 12, 1, 0, 10),
(35, 53, 'simples', 'Porção de Polenta Frita (500g) 🌽', 'Polenta frita', 'polenta-frita-500', 28.90, NULL, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Polenta', 12, 1, 0, 11),
(35, 53, 'simples', 'Amendoim (300g) 🥜', 'Amendoim torrado', 'amendoim-300', 16.90, NULL, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Amendoim', 3, 1, 0, 12),
(35, 53, 'simples', 'Azeitonas Temperadas (200g) ', 'Azeitonas temperadas', 'azeitonas-200', 18.90, NULL, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Azeitonas, alho, ervas', 3, 1, 0, 13),

-- BEBIDAS (subcategoria 14) - 7 produtos
(35, 14, 'simples', 'Cerveja Long Neck (355ml) 🍺', 'Cerveja gelada', 'cerveja-long-355', 9.90, NULL, 'https://images.unsplash.com/photo-1586994496097-9c0fb7a509ac?w=300', 'Cerveja', 2, 1, 1, 14),
(35, 14, 'simples', 'Cerveja Long Neck (6 unidades) 🍺🍺', 'Pack com 6 cervejas', 'cerveja-pack-6', 54.90, 49.90, 'https://images.unsplash.com/photo-1586994496097-9c0fb7a509ac?w=300', 'Cerveja', 2, 1, 1, 15),
(35, 14, 'simples', 'Cerveja 600ml 🍺', 'Cerveja long neck', 'cerveja-600', 16.90, NULL, 'https://images.unsplash.com/photo-1586994496097-9c0fb7a509ac?w=300', 'Cerveja', 2, 1, 0, 16),
(35, 14, 'simples', 'Chopp (500ml) 🍺', 'Chopp gelado', 'chopp-500', 14.90, 12.90, 'https://images.unsplash.com/photo-1586994496097-9c0fb7a509ac?w=300', 'Chopp', 3, 1, 1, 17),
(35, 14, 'simples', 'Caipirinha de Limão (500ml) 🍋', 'Caipirinha tradicional', 'caipirinha-500', 24.90, 21.90, 'https://images.unsplash.com/photo-1513558161293-e3d4df538c9b?w=300', 'Limão, cachaça, açúcar, gelo', 5, 1, 1, 18),
(35, 14, 'simples', 'Caipirinha de Frutas (500ml) 🍓', 'Caipirinha de frutas vermelhas', 'caipirinha-frutas-500', 28.90, 24.90, 'https://images.unsplash.com/photo-1513558161293-e3d4df538c9b?w=300', 'Frutas vermelhas, cachaça, açúcar, gelo', 5, 1, 1, 19),
(35, 14, 'simples', 'Refrigerante Lata (350ml) 🥤', 'Coca-Cola, Guaraná, Sprite', 'refrigerante-lata-350', 6.90, NULL, 'https://images.unsplash.com/photo-1581006852262-4305c2fad871?w=300', 'Refrigerante', 2, 1, 0, 20);