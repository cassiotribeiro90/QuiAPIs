-- =====================================================
-- LOJA 1: Pizzaria do João (35 produtos)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PIZZAS SALGADAS (subcategoria 1) - 15 produtos
(1, 1, 'personalizavel', 'Pizza Calabresa do João 🌶️', 'Molho especial, mussarela, calabresa artesanal, cebola roxa', 'pizza-calabresa-joao', 45.90, 39.90, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho de tomate italiano, mussarela, calabresa artesanal, cebola roxa, orégano', 30, 1, 1, 1),
(1, 1, 'personalizavel', 'Pizza Margherita do João 🧀', 'Molho, mussarela de búfala, tomate italiano, manjericão', 'pizza-margherita-joao', 42.90, 37.90, 'https://images.unsplash.com/photo-1604068549290-dea0e4a305ca?w=300', 'Molho, mussarela de búfala, tomate italiano, manjericão', 25, 1, 1, 2),
(1, 1, 'personalizavel', 'Pizza Portuguesa do João 🇵🇹', 'Molho, mussarela, presunto, ovos, cebola, pimentão, azeitona', 'pizza-portuguesa-joao', 48.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, presunto, ovos, cebola, pimentão, azeitona', 35, 1, 1, 3),
(1, 1, 'personalizavel', 'Pizza Frango com Catupiry do João 🐔', 'Molho, mussarela, frango desfiado, catupiry', 'pizza-frango-catupiry-joao', 49.90, 44.90, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, frango desfiado, catupiry', 30, 1, 1, 4),
(1, 1, 'personalizavel', 'Pizza Pepperoni do João 🍖', 'Molho, mussarela, pepperoni, orégano', 'pizza-pepperoni-joao', 47.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, pepperoni, orégano', 30, 1, 0, 5),
(1, 1, 'personalizavel', 'Pizza Quatro Queijos do João 🧀🧀', 'Mussarela, provolone, parmesão, catupiry', 'pizza-quatro-queijos-joao', 52.90, NULL, 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=300', 'Mussarela, provolone, parmesão, catupiry', 30, 1, 1, 6),
(1, 1, 'personalizavel', 'Pizza Napolitana do João 🍕', 'Molho, mussarela, tomate, manjericão, parmesão', 'pizza-napolitana-joao', 46.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, tomate, manjericão, parmesão', 30, 1, 0, 7),
(1, 1, 'personalizavel', 'Pizza Bacon do João 🥓', 'Molho, mussarela, bacon crocante, cebola', 'pizza-bacon-joao', 48.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, bacon crocante, cebola', 30, 1, 0, 8),
(1, 1, 'personalizavel', 'Pizza Milho com Bacon do João 🌽', 'Molho, mussarela, milho, bacon', 'pizza-milho-bacon-joao', 46.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, milho, bacon', 30, 1, 0, 9),
(1, 1, 'personalizavel', 'Pizza Alho e Óleo do João 🧄', 'Molho, mussarela, alho frito, parmesão', 'pizza-alho-oleo-joao', 44.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, alho frito, parmesão', 25, 1, 0, 10),
(1, 1, 'personalizavel', 'Pizza Rúcula com Tomate Seco do João 🌿', 'Molho, mussarela, rúcula, tomate seco, parmesão', 'pizza-rucula-tomate-seco-joao', 51.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, rúcula, tomate seco, parmesão', 30, 1, 1, 11),
(1, 1, 'personalizavel', 'Pizza Palmito do João 🌴', 'Molho, mussarela, palmito, azeitona', 'pizza-palmito-joao', 49.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, palmito, azeitona', 30, 1, 0, 12),
(1, 1, 'personalizavel', 'Pizza Atum do João 🐟', 'Molho, mussarela, atum, cebola', 'pizza-atum-joao', 47.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, atum, cebola', 30, 1, 0, 13),
(1, 1, 'personalizavel', 'Pizza Escarola do João 🥬', 'Molho, mussarela, escarola, bacon', 'pizza-escarola-joao', 48.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, escarola, bacon', 30, 1, 0, 14),
(1, 1, 'personalizavel', 'Pizza Vegana do João 🌱', 'Molho, tofu, cogumelos, rúcula, tomate seco', 'pizza-vegana-joao', 49.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, tofu, cogumelos, rúcula, tomate seco', 30, 1, 1, 15),

-- PIZZAS DOCES (subcategoria 2) - 8 produtos
(1, 2, 'personalizavel', 'Pizza Chocolate do João 🍫', 'Chocolate ao leite, granulado, morango', 'pizza-chocolate-joao', 39.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate ao leite, granulado, morango', 20, 1, 1, 16),
(1, 2, 'personalizavel', 'Pizza Banana com Canela do João 🍌', 'Banana, canela, leite condensado', 'pizza-banana-canela-joao', 37.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Banana, canela, leite condensado', 20, 1, 1, 17),
(1, 2, 'personalizavel', 'Pizza Romeu e Julieta do João 🧀🍐', 'Mussarela, goiabada', 'pizza-romeu-julieta-joao', 41.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Mussarela, goiabada', 20, 1, 0, 18),
(1, 2, 'personalizavel', 'Pizza Prestígio do João 🥥', 'Chocolate, coco ralado, leite condensado', 'pizza-prestigio-joao', 42.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate, coco ralado, leite condensado', 20, 1, 0, 19),
(1, 2, 'personalizavel', 'Pizza Sensação do João 🍓', 'Chocolate, morango, leite condensado', 'pizza-sensacao-joao', 42.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate, morango, leite condensado', 20, 1, 1, 20),
(1, 2, 'personalizavel', 'Pizza Brigadeiro do João 🍬', 'Brigadeiro, granulado', 'pizza-brigadeiro-joao', 40.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Brigadeiro, granulado', 20, 1, 0, 21),
(1, 2, 'personalizavel', 'Pizza Doce de Leite do João 🥛', 'Doce de leite, coco', 'pizza-doce-leite-joao', 41.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Doce de leite, coco', 20, 1, 0, 22),
(1, 2, 'personalizavel', 'Pizza Banana Nevada do João 🍌❄️', 'Banana, chocolate branco, canela', 'pizza-banana-nevada-joao', 43.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Banana, chocolate branco, canela', 20, 1, 0, 23),

-- MEIO A MEIO (subcategoria 3) - 4 produtos
(1, 3, 'personalizavel', 'Pizza Meio Calabresa Meio Margherita 🤝', 'Dois sabores na mesma pizza', 'pizza-meio-calabresa-margherita-joao', 55.90, 49.90, 'https://images.unsplash.com/photo-1604911954692-97c257f2a3d0?w=300', 'Calabresa e Margherita', 40, 1, 1, 24),
(1, 3, 'personalizavel', 'Pizza Meio Frango Meio Portuguesa 🤝', 'Dois sabores na mesma pizza', 'pizza-meio-frango-portuguesa-joao', 56.90, NULL, 'https://images.unsplash.com/photo-1604911954692-97c257f2a3d0?w=300', 'Frango com Catupiry e Portuguesa', 40, 1, 1, 25),
(1, 3, 'personalizavel', 'Pizza Meio Pepperoni Meio Quatro Queijos 🤝', 'Dois sabores na mesma pizza', 'pizza-meio-pepperoni-quatro-queijos-joao', 57.90, NULL, 'https://images.unsplash.com/photo-1604911954692-97c257f2a3d0?w=300', 'Pepperoni e Quatro Queijos', 40, 1, 0, 26),
(1, 3, 'personalizavel', 'Pizza Meio Chocolate Meio Banana 🤝', 'Dois sabores doces na mesma pizza', 'pizza-meio-chocolate-banana-joao', 49.90, NULL, 'https://images.unsplash.com/photo-1604911954692-97c257f2a3d0?w=300', 'Chocolate e Banana com Canela', 35, 1, 1, 27),

-- BORDAS RECHEADAS (subcategoria 4) - 4 produtos
(1, 4, 'personalizavel', 'Pizza Calabresa com Borda Catupiry 🧀', 'Pizza calabresa com borda recheada de catupiry', 'pizza-calabresa-borda-catupiry-joao', 52.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza calabresa com borda de catupiry', 35, 1, 1, 28),
(1, 4, 'personalizavel', 'Pizza Margherita com Borda Cheddar 🟨', 'Pizza margherita com borda recheada de cheddar', 'pizza-margherita-borda-cheddar-joao', 51.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza margherita com borda de cheddar', 35, 1, 0, 29),
(1, 4, 'personalizavel', 'Pizza Chocolate com Borda Chocolate 🍫', 'Pizza chocolate com borda recheada de chocolate', 'pizza-chocolate-borda-chocolate-joao', 49.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza chocolate com borda de chocolate', 30, 1, 1, 30),
(1, 4, 'personalizavel', 'Pizza Quatro Queijos com Borda Catupiry 🧀', 'Pizza quatro queijos com borda de catupiry', 'pizza-quatro-queijos-borda-catupiry-joao', 56.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza quatro queijos com borda de catupiry', 35, 1, 0, 31),

-- CALZONES (subcategoria 5) - 4 produtos
(1, 5, 'simples', 'Calzone Calabresa do João 🥟', 'Massa de pizza recheada com calabresa e mussarela', 'calzone-calabresa-joao', 38.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com calabresa, mussarela, molho', 25, 1, 1, 32),
(1, 5, 'simples', 'Calzone Frango com Catupiry do João 🥟', 'Massa de pizza recheada com frango e catupiry', 'calzone-frango-catupiry-joao', 40.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com frango, catupiry', 25, 1, 1, 33),
(1, 5, 'simples', 'Calzone Quatro Queijos do João 🥟', 'Massa de pizza recheada com quatro queijos', 'calzone-quatro-queijos-joao', 41.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com mussarela, provolone, parmesão, catupiry', 25, 1, 0, 34),
(1, 5, 'simples', 'Calzone Doce de Chocolate do João 🥟', 'Massa de pizza recheada com chocolate', 'calzone-chocolate-joao', 36.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com chocolate', 20, 1, 0, 35);


-- =====================================================
-- LOJA 2: Dominus Pizza (35 produtos premium)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PIZZAS SALGADAS PREMIUM (subcategoria 1) - 20 produtos
(2, 1, 'personalizavel', 'Pizza Dominus Especial 👑', 'Molho secreto da casa, mussarela premium, pepperoni, cogumelos, azeitona', 'pizza-dominus-especial', 62.90, 54.90, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho especial, mussarela premium, pepperoni, cogumelos frescos, azeitona', 35, 1, 1, 1),
(2, 1, 'personalizavel', 'Pizza Pepperoni Premium 🍖', 'Molho, mussarela, pepperoni importado, orégano', 'pizza-pepperoni-premium', 54.90, 49.90, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho de tomate italiano, mussarela, pepperoni importado, orégano', 30, 1, 1, 2),
(2, 1, 'personalizavel', 'Pizza Quatro Queijos Premium 🧀', 'Mussarela de búfala, provolone, gorgonzola, parmesão', 'pizza-quatro-queijos-premium', 59.90, NULL, 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=300', 'Mussarela de búfala, provolone, gorgonzola, parmesão', 30, 1, 1, 3),
(2, 1, 'personalizavel', 'Pizza Calabresa Especial 🌶️', 'Calabresa artesanal, cebola roxa, catupiry', 'pizza-calabresa-especial', 52.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, calabresa artesanal, cebola roxa, catupiry', 30, 1, 1, 4),
(2, 1, 'personalizavel', 'Pizza Frango com Catupiry Premium 🐔', 'Frango desfiado, catupiry, milho, azeitona', 'pizza-frango-catupiry-premium', 53.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, frango desfiado, catupiry, milho, azeitona', 30, 1, 0, 5),
(2, 1, 'personalizavel', 'Pizza Portuguesa Premium 🇵🇹', 'Presunto, ovos, cebola, pimentão, ervilha, azeitona', 'pizza-portuguesa-premium', 54.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, presunto, ovos, cebola, pimentão, ervilha, azeitona', 35, 1, 0, 6),
(2, 1, 'personalizavel', 'Pizza Margherita Premium 🧀', 'Mussarela de búfala, tomate italiano, manjericão fresco', 'pizza-margherita-premium', 49.90, NULL, 'https://images.unsplash.com/photo-1604068549290-dea0e4a305ca?w=300', 'Molho, mussarela de búfala, tomate italiano, manjericão', 25, 1, 1, 7),
(2, 1, 'personalizavel', 'Pizza Bacon Supremo 🥓', 'Bacon crocante, champignon, cebola', 'pizza-bacon-supremo', 55.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, bacon crocante, champignon, cebola', 30, 1, 0, 8),
(2, 1, 'personalizavel', 'Pizza Rúcula com Tomate Seco Premium 🌿', 'Rúcula, tomate seco, parmesão, mussarela', 'pizza-rucula-tomate-seco-premium', 56.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, rúcula, tomate seco, parmesão', 30, 1, 1, 9),
(2, 1, 'personalizavel', 'Pizza Palmito Premium 🌴', 'Palmito, azeitona, cebola, mussarela', 'pizza-palmito-premium', 53.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, palmito, azeitona, cebola', 30, 1, 0, 10),
(2, 1, 'personalizavel', 'Pizza Alho e Óleo com Parmesão 🧄', 'Alho frito, parmesão, mussarela', 'pizza-alho-oleo-parmesao', 48.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, alho frito, parmesão', 25, 1, 0, 11),
(2, 1, 'personalizavel', 'Pizza Atum com Cebola 🐟', 'Atum sólido, cebola roxa, azeitona', 'pizza-atum-cebola', 50.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, atum sólido, cebola roxa, azeitona', 30, 1, 0, 12),
(2, 1, 'personalizavel', 'Pizza Milho com Bacon Premium 🌽', 'Milho verde, bacon crocante, mussarela', 'pizza-milho-bacon-premium', 51.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, milho verde, bacon crocante', 30, 1, 0, 13),
(2, 1, 'personalizavel', 'Pizza Escarola com Bacon Premium 🥬', 'Escarola refogada, bacon, mussarela', 'pizza-escarola-bacon-premium', 52.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, escarola refogada, bacon', 30, 1, 0, 14),
(2, 1, 'personalizavel', 'Pizza Salmão com Cream Cheese 🐟', 'Salmão defumado, cream cheese, alcaparras, cebola roxa', 'pizza-salmao-cream-cheese', 69.90, 59.90, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, salmão defumado, cream cheese, alcaparras, cebola roxa', 35, 1, 1, 15),
(2, 1, 'personalizavel', 'Pizza Trufada com Cogumelos 🍄', 'Azeite trufado, cogumelos frescos, mussarela, parmesão', 'pizza-trufada-cogumelos', 64.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Azeite trufado, cogumelos frescos, mussarela, parmesão', 35, 1, 1, 16),
(2, 1, 'personalizavel', 'Pizza Parma com Rúcula 🇮🇹', 'Presunto de parma, rúcula, mussarela, parmesão', 'pizza-parma-rucula', 66.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, presunto de parma, rúcula, parmesão', 30, 1, 1, 17),
(2, 1, 'personalizavel', 'Pizza Provolone com Alho 🧄', 'Provolone defumado, alho assado, alecrim', 'pizza-provolone-alho', 57.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, provolone defumado, alho assado, alecrim', 30, 1, 0, 18),
(2, 1, 'personalizavel', 'Pizza Gorgonzola com Nozes 🧀', 'Gorgonzola, nozes, mel, mussarela', 'pizza-gorgonzola-nozes', 61.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, gorgonzola, nozes, mel', 30, 1, 1, 19),
(2, 1, 'personalizavel', 'Pizza Vegana Especial 🌱', 'Molho, tofu, cogumelos, rúcula, tomate seco, azeitona', 'pizza-vegana-especial', 54.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, tofu, cogumelos, rúcula, tomate seco, azeitona', 30, 1, 1, 20),

-- PIZZAS DOCES PREMIUM (subcategoria 2) - 8 produtos
(2, 2, 'personalizavel', 'Pizza Chocolate Belga 🍫', 'Chocolate belga, morango, granulado', 'pizza-chocolate-belga', 45.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate belga, morango, granulado', 20, 1, 1, 21),
(2, 2, 'personalizavel', 'Pizza Banana Caramelizada 🍌', 'Banana caramelizada, canela, leite condensado', 'pizza-banana-caramelizada', 43.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Banana caramelizada, canela, leite condensado', 20, 1, 1, 22),
(2, 2, 'personalizavel', 'Pizza Romeu e Julieta Premium 🧀🍐', 'Queijo minas, goiabada cascão', 'pizza-romeu-julieta-premium', 46.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Queijo minas, goiabada cascão', 20, 1, 0, 23),
(2, 2, 'personalizavel', 'Pizza Prestígio Premium 🥥', 'Chocolate, coco queimado, leite condensado', 'pizza-prestigio-premium', 47.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate, coco queimado, leite condensado', 20, 1, 1, 24),
(2, 2, 'personalizavel', 'Pizza Sensação Premium 🍓', 'Chocolate, morango, leite condensado', 'pizza-sensacao-premium', 47.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate, morango, leite condensado', 20, 1, 1, 25),
(2, 2, 'personalizavel', 'Pizza Doce de Leite com Nozes 🥛', 'Doce de leite, nozes, coco', 'pizza-doce-leite-nozes', 48.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Doce de leite, nozes, coco', 20, 1, 0, 26),
(2, 2, 'personalizavel', 'Pizza Brownie com Sorvete 🍫', 'Brownie, sorvete de creme, calda de chocolate', 'pizza-brownie-sorvete', 52.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Brownie, sorvete de creme, calda de chocolate', 25, 1, 1, 27),
(2, 2, 'personalizavel', 'Pizza Maçã com Canela 🍎', 'Maçã caramelizada, canela, açúcar mascavo', 'pizza-maca-canela', 44.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Maçã caramelizada, canela, açúcar mascavo', 20, 1, 0, 28),

-- BORDAS RECHEADAS PREMIUM (subcategoria 4) - 4 produtos
(2, 4, 'personalizavel', 'Pizza Dominus com Borda Catupiry 🧀', 'Pizza Dominus Especial com borda de catupiry', 'pizza-dominus-borda-catupiry', 68.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza Dominus Especial com borda de catupiry', 35, 1, 1, 29),
(2, 4, 'personalizavel', 'Pizza Pepperoni com Borda Cheddar 🟨', 'Pizza pepperoni com borda de cheddar', 'pizza-pepperoni-borda-cheddar', 60.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza pepperoni com borda de cheddar', 35, 1, 1, 30),
(2, 4, 'personalizavel', 'Pizza Quatro Queijos com Borda Gorgonzola 🧀', 'Pizza quatro queijos com borda de gorgonzola', 'pizza-quatro-queijos-borda-gorgonzola', 66.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza quatro queijos com borda de gorgonzola', 35, 1, 0, 31),
(2, 4, 'personalizavel', 'Pizza Chocolate com Borda Chocolate 🍫', 'Pizza chocolate com borda de chocolate', 'pizza-chocolate-borda-chocolate-premium', 51.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza chocolate com borda de chocolate', 30, 1, 1, 32),

-- CALZONES PREMIUM (subcategoria 5) - 3 produtos
(2, 5, 'simples', 'Calzone Dominus Especial 🥟', 'Calzone recheado com pepperoni, cogumelos, mussarela', 'calzone-dominus-especial', 45.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com pepperoni, cogumelos, mussarela, molho', 25, 1, 1, 33),
(2, 5, 'simples', 'Calzone Quatro Queijos Premium 🥟', 'Calzone recheado com quatro queijos', 'calzone-quatro-queijos-premium', 46.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com mussarela, provolone, gorgonzola, parmesão', 25, 1, 1, 34),
(2, 5, 'simples', 'Calzone Doce de Chocolate Belga 🥟', 'Calzone recheado com chocolate belga', 'calzone-chocolate-belga', 42.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com chocolate belga', 20, 1, 0, 35);



-- =====================================================
-- LOJA 3: Pizza Prime (35 produtos - foco em pizzas doces)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PIZZAS DOCES (subcategoria 2) - 20 produtos (especialidade da casa)
(3, 2, 'personalizavel', 'Pizza Chocolate com Morango Prime 🍓', 'Chocolate ao leite, morangos frescos, granulado colorido', 'pizza-chocolate-morango-prime', 42.90, 37.90, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate ao leite, morangos frescos, granulado colorido', 20, 1, 1, 1),
(3, 2, 'personalizavel', 'Pizza Banana com Canela Prime 🍌', 'Banana caramelizada, canela, leite condensado', 'pizza-banana-canela-prime', 39.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Banana caramelizada, canela, leite condensado', 20, 1, 1, 2),
(3, 2, 'personalizavel', 'Pizza Romeu e Julieta Prime 🧀🍐', 'Queijo minas, goiabada cascão, cream cheese', 'pizza-romeu-julieta-prime', 44.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Queijo minas, goiabada cascão, cream cheese', 20, 1, 1, 3),
(3, 2, 'personalizavel', 'Pizza Prestígio Prime 🥥', 'Chocolate ao leite, coco fresco ralado, leite condensado', 'pizza-prestigio-prime', 43.90, 38.90, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate ao leite, coco fresco ralado, leite condensado', 20, 1, 1, 4),
(3, 2, 'personalizavel', 'Pizza Sensação Prime 🍓', 'Chocolate ao leite, morango, leite condensado, granulado', 'pizza-sensacao-prime', 44.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate ao leite, morango, leite condensado, granulado', 20, 1, 1, 5),
(3, 2, 'personalizavel', 'Pizza Doce de Leite com Coco Prime 🥛', 'Doce de leite, coco queimado', 'pizza-doce-leite-coco-prime', 41.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Doce de leite, coco queimado', 20, 1, 0, 6),
(3, 2, 'personalizavel', 'Pizza Chocolate Branco com Framboesa 🍫', 'Chocolate branco, framboesas frescas, raspas de limão', 'pizza-chocolate-branco-framboesa', 46.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate branco, framboesas frescas, raspas de limão', 20, 1, 1, 7),
(3, 2, 'personalizavel', 'Pizza Nutella com Morango 🍫', 'Nutella cremosa, morangos frescos, avelãs picadas', 'pizza-nutella-morango', 49.90, 44.90, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Nutella, morangos frescos, avelãs picadas', 20, 1, 1, 8),
(3, 2, 'personalizavel', 'Pizza Oreo com Chocolate 🍪', 'Chocolate ao leite, biscoitos Oreo, leite condensado', 'pizza-oreo-chocolate', 45.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate ao leite, biscoitos Oreo, leite condensado', 20, 1, 1, 9),
(3, 2, 'personalizavel', 'Pizza M&Ms Colorida 🎨', 'Chocolate ao leite, M&Ms coloridos, granulado', 'pizza-mms-colorida', 44.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate ao leite, M&Ms coloridos, granulado', 20, 1, 0, 10),
(3, 2, 'personalizavel', 'Pizza Paçoca com Doce de Leite 🥜', 'Doce de leite, paçoca esfarelada', 'pizza-pacoca-doce-leite', 42.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Doce de leite, paçoca esfarelada', 20, 1, 1, 11),
(3, 2, 'personalizavel', 'Pizza Banana com Nutella 🍌', 'Banana, Nutella, canela', 'pizza-banana-nutella', 46.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Banana, Nutella, canela', 20, 1, 1, 12),
(3, 2, 'personalizavel', 'Pizza Chocolate com Coco e Leite Condensado 🥥', 'Chocolate, coco, leite condensado', 'pizza-chocolate-coco-condensado', 43.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate, coco, leite condensado', 20, 1, 0, 13),
(3, 2, 'personalizavel', 'Pizza Maçã com Canela e Doce de Leite 🍎', 'Maçã caramelizada, canela, doce de leite', 'pizza-maca-canela-doce-leite', 42.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Maçã caramelizada, canela, doce de leite', 20, 1, 0, 14),
(3, 2, 'personalizavel', 'Pizza Abacaxi com Coco e Leite Condensado 🍍', 'Abacaxi caramelizado, coco, leite condensado', 'pizza-abacaxi-coco', 41.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Abacaxi caramelizado, coco, leite condensado', 20, 1, 0, 15),
(3, 2, 'personalizavel', 'Pizza Chocolate com Morango e Banana 🍫', 'Chocolate, morango, banana, leite condensado', 'pizza-chocolate-morango-banana', 46.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate, morango, banana, leite condensado', 20, 1, 1, 16),
(3, 2, 'personalizavel', 'Pizza Ninho com Morango 🥛', 'Creme de leite Ninho, morangos frescos', 'pizza-ninho-morango', 44.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Creme de leite Ninho, morangos frescos', 20, 1, 1, 17),
(3, 2, 'personalizavel', 'Pizza Kinder Ovo 🥚', 'Chocolate ao leite, creme Kinder, confeitos', 'pizza-kinder-ovo', 48.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate ao leite, creme Kinder, confeitos', 20, 1, 1, 18),
(3, 2, 'personalizavel', 'Pizza Ferrero Rocher 🌰', 'Chocolate, avelãs, Nutella, creme de avelã', 'pizza-ferrero-rocher', 51.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Chocolate, avelãs, Nutella, creme de avelã', 20, 1, 1, 19),
(3, 2, 'personalizavel', 'Pizza Brigadeiro com Granulado 🍬', 'Brigadeiro cremoso, granulado colorido', 'pizza-brigadeiro-granulado', 40.90, NULL, 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=300', 'Brigadeiro cremoso, granulado colorido', 20, 1, 0, 20),

-- PIZZAS SALGADAS (subcategoria 1) - 8 produtos
(3, 1, 'personalizavel', 'Pizza Calabresa Prime 🌶️', 'Molho especial, mussarela, calabresa, cebola roxa', 'pizza-calabresa-prime', 46.90, 41.90, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho especial, mussarela, calabresa, cebola roxa, orégano', 30, 1, 1, 21),
(3, 1, 'personalizavel', 'Pizza Margherita Prime 🧀', 'Molho, mussarela de búfala, tomate, manjericão', 'pizza-margherita-prime', 45.90, NULL, 'https://images.unsplash.com/photo-1604068549290-dea0e4a305ca?w=300', 'Molho, mussarela de búfala, tomate, manjericão', 25, 1, 1, 22),
(3, 1, 'personalizavel', 'Pizza Frango com Catupiry Prime 🐔', 'Frango desfiado, catupiry, milho', 'pizza-frango-catupiry-prime', 48.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, frango desfiado, catupiry, milho', 30, 1, 0, 23),
(3, 1, 'personalizavel', 'Pizza Portuguesa Prime 🇵🇹', 'Presunto, ovos, cebola, pimentão, azeitona', 'pizza-portuguesa-prime', 49.90, NULL, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300', 'Molho, mussarela, presunto, ovos, cebola, pimentão, azeitona', 35, 1, 0, 24),
(3, 1, 'personalizavel', 'Pizza Quatro Queijos Prime 🧀', 'Mussarela, provolone, parmesão, catupiry', 'pizza-quatro-queijos-prime', 51.90, NULL, 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=300', 'Mussarela, provolone, parmesão, catupiry', 30, 1, 1, 25),
(3, 1, 'personalizavel', 'Pizza Bacon Prime 🥓', 'Bacon crocante, mussarela, cebola', 'pizza-bacon-prime', 47.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, bacon crocante, cebola', 30, 1, 0, 26),
(3, 1, 'personalizavel', 'Pizza Pepperoni Prime 🍖', 'Pepperoni, mussarela, orégano', 'pizza-pepperoni-prime', 48.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, pepperoni, orégano', 30, 1, 0, 27),
(3, 1, 'personalizavel', 'Pizza Rúcula com Tomate Seco Prime 🌿', 'Rúcula, tomate seco, mussarela, parmesão', 'pizza-rucula-tomate-seco-prime', 50.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Molho, mussarela, rúcula, tomate seco, parmesão', 30, 1, 1, 28),

-- MEIO A MEIO (subcategoria 3) - 4 produtos (combos doces+doces ou doces+salgados)
(3, 3, 'personalizavel', 'Pizza Meio Chocolate Meio Morango 🍫🍓', 'Chocolate ao leite e morangos frescos', 'pizza-meio-chocolate-morango', 49.90, 44.90, 'https://images.unsplash.com/photo-1604911954692-97c257f2a3d0?w=300', 'Chocolate ao leite e morangos frescos', 35, 1, 1, 29),
(3, 3, 'personalizavel', 'Pizza Meio Banana Meio Chocolate Branco 🍌🍫', 'Banana caramelizada e chocolate branco', 'pizza-meio-banana-chocolate-branco', 48.90, NULL, 'https://images.unsplash.com/photo-1604911954692-97c257f2a3d0?w=300', 'Banana caramelizada e chocolate branco', 35, 1, 1, 30),
(3, 3, 'personalizavel', 'Pizza Meio Calabresa Meio Chocolate 🍕🍫', 'Calabresa e chocolate (doce e salgado)', 'pizza-meio-calabresa-chocolate', 52.90, NULL, 'https://images.unsplash.com/photo-1604911954692-97c257f2a3d0?w=300', 'Calabresa e chocolate ao leite', 40, 1, 1, 31),
(3, 3, 'personalizavel', 'Pizza Meio Frango Meio Chocolate 🐔🍫', 'Frango com catupiry e chocolate', 'pizza-meio-frango-chocolate', 53.90, NULL, 'https://images.unsplash.com/photo-1604911954692-97c257f2a3d0?w=300', 'Frango com catupiry e chocolate ao leite', 40, 1, 0, 32),

-- BORDAS RECHEADAS DOCES (subcategoria 4) - 3 produtos
(3, 4, 'personalizavel', 'Pizza Chocolate com Borda Doce de Leite 🥛', 'Pizza de chocolate com borda de doce de leite', 'pizza-chocolate-borda-doce-leite', 48.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza de chocolate com borda de doce de leite', 30, 1, 1, 33),
(3, 4, 'personalizavel', 'Pizza Banana com Borda Chocolate 🍫', 'Pizza de banana com borda de chocolate', 'pizza-banana-borda-chocolate', 47.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza de banana com borda de chocolate', 30, 1, 1, 34),
(3, 4, 'personalizavel', 'Pizza Nutella com Borda Ninho 🥛', 'Pizza de Nutella com borda de creme Ninho', 'pizza-nutella-borda-ninho', 53.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza de Nutella com borda de creme Ninho', 30, 1, 1, 35);


-- =====================================================
-- LOJA 4: La Pizza Mia (35 produtos - pizzas napolitanas)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PIZZAS NAPOLITANAS TRADICIONAIS (subcategoria 1) - 25 produtos
(4, 1, 'personalizavel', 'Pizza Margherita Napolitana 🧀', 'Molho San Marzano, mussarela de búfala, manjericão fresco, parmesão', 'margherita-napolitana', 58.90, 52.90, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho San Marzano, mussarela de búfala, manjericão fresco, parmesão, azeite', 35, 1, 1, 1),
(4, 1, 'personalizavel', 'Pizza Marinara 🍅', 'Molho San Marzano, alho, orégano, manjericão', 'marinara', 48.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho San Marzano, alho, orégano, manjericão, azeite', 30, 1, 1, 2),
(4, 1, 'personalizavel', 'Pizza Diavola 🔥', 'Molho, mussarela, salame picante, pimenta calabresa', 'diavola', 62.90, 56.90, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho San Marzano, mussarela, salame picante, pimenta calabresa, azeite', 35, 1, 1, 3),
(4, 1, 'personalizavel', 'Pizza Quattro Formaggi 🧀', 'Mussarela, gorgonzola, parmesão, provolone', 'quattro-formaggi', 64.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Mussarela, gorgonzola, parmesão, provolone, orégano', 35, 1, 1, 4),
(4, 1, 'personalizavel', 'Pizza Prosciutto e Funghi 🍖', 'Presunto cru, cogumelos frescos, mussarela', 'prosciutto-funghi', 66.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, presunto cru, cogumelos frescos, parmesão', 40, 1, 1, 5),
(4, 1, 'personalizavel', 'Pizza Capricciosa 🎭', 'Molho, mussarela, presunto, cogumelos, alcachofra, azeitona', 'capricciosa', 63.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, presunto, cogumelos, alcachofra, azeitona', 40, 1, 0, 6),
(4, 1, 'personalizavel', 'Pizza Napoli 🇮🇹', 'Molho, mussarela, anchovas, alcaparras, azeitona', 'napoli', 59.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, anchovas, alcaparras, azeitona, orégano', 35, 1, 0, 7),
(4, 1, 'personalizavel', 'Pizza Calabresa Napolitana 🌶️', 'Calabresa artesanal, cebola roxa, mussarela', 'calabresa-napolitana', 56.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, calabresa artesanal, cebola roxa, orégano', 35, 1, 1, 8),
(4, 1, 'personalizavel', 'Pizza Pepperoni Napolitana 🍖', 'Pepperoni importado, mussarela, orégano', 'pepperoni-napolitana', 59.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, pepperoni importado, orégano', 35, 1, 0, 9),
(4, 1, 'personalizavel', 'Pizza Salsiccia e Friarielli 🌿', 'Linguiça italiana, brócolis italiano, mussarela', 'salsiccia-friarielli', 65.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, linguiça italiana, brócolis italiano, parmesão', 40, 1, 1, 10),
(4, 1, 'personalizavel', 'Pizza Parmigiana 🍆', 'Molho, mussarela, berinjela empanada, parmesão, manjericão', 'parmigiana', 61.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, berinjela empanada, parmesão, manjericão', 40, 1, 0, 11),
(4, 1, 'personalizavel', 'Pizza Bianca 🤍', 'Mussarela, gorgonzola, parmesão, alecrim, sem molho', 'bianca', 58.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Mussarela, gorgonzola, parmesão, alecrim, azeite', 30, 1, 1, 12),
(4, 1, 'personalizavel', 'Pizza Fungi 🍄', 'Molho, mussarela, mix de cogumelos frescos, parmesão, trufa', 'fungi', 67.90, 59.90, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, mix de cogumelos frescos, parmesão, azeite trufado', 40, 1, 1, 13),
(4, 1, 'personalizavel', 'Pizza Parma e Rúcula 🇮🇹', 'Presunto de parma, rúcula, mussarela, parmesão', 'parma-rucula', 68.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, presunto de parma, rúcula, parmesão', 35, 1, 1, 14),
(4, 1, 'personalizavel', 'Pizza Gorgonzola com Nozes 🧀', 'Gorgonzola, nozes, mel, mussarela', 'gorgonzola-nozes', 63.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, gorgonzola, nozes, mel', 35, 1, 0, 15),
(4, 1, 'personalizavel', 'Pizza Carbonara 🍳', 'Molho, mussarela, guanciale, gema, queijo pecorino', 'carbonara', 64.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, guanciale, gema, queijo pecorino, pimenta', 40, 1, 1, 16),
(4, 1, 'personalizavel', 'Pizza Frango com Catupiry Napolitana 🐔', 'Frango desfiado, catupiry, mussarela', 'frango-catupiry-napolitana', 57.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, frango desfiado, catupiry', 35, 1, 0, 17),
(4, 1, 'personalizavel', 'Pizza Alho e Óleo com Parmesão 🧄', 'Alho confitado, parmesão, mussarela, salsinha', 'alho-oleo-parmesao', 54.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, alho confitado, parmesão, salsinha', 30, 1, 0, 18),
(4, 1, 'personalizavel', 'Pizza Rúcula com Tomate Seco e Parmesão 🌿', 'Rúcula, tomate seco, mussarela, parmesão', 'rucula-tomate-seco', 59.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, rúcula, tomate seco, parmesão', 35, 1, 1, 19),
(4, 1, 'personalizavel', 'Pizza Palmito com Catupiry 🌴', 'Palmito, catupiry, mussarela', 'palmito-catupiry', 58.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, palmito, catupiry', 35, 1, 0, 20),
(4, 1, 'personalizavel', 'Pizza Atum com Cebola Roxa 🐟', 'Atum sólido, cebola roxa, mussarela, azeitona', 'atum-cebola-roxa', 56.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, atum sólido, cebola roxa, azeitona', 35, 1, 0, 21),
(4, 1, 'personalizavel', 'Pizza Portuguesa Napolitana 🇵🇹', 'Presunto, ovos, cebola, pimentão, ervilha, azeitona', 'portuguesa-napolitana', 59.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, presunto, ovos, cebola, pimentão, ervilha, azeitona', 40, 1, 0, 22),
(4, 1, 'personalizavel', 'Pizza Milho com Bacon 🌽', 'Milho verde, bacon crocante, mussarela', 'milho-bacon', 55.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, milho verde, bacon crocante', 35, 1, 0, 23),
(4, 1, 'personalizavel', 'Pizza Escarola com Bacon 🥬', 'Escarola refogada, bacon, mussarela', 'escarola-bacon', 57.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, mussarela, escarola refogada, bacon', 35, 1, 0, 24),
(4, 1, 'personalizavel', 'Pizza Vegana Napolitana 🌱', 'Molho, tofu, cogumelos, rúcula, tomate seco, azeitona', 'vegana-napolitana', 58.90, NULL, 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=300', 'Molho, tofu, cogumelos, rúcula, tomate seco, azeitona', 35, 1, 1, 25),

-- CALZONES (subcategoria 5) - 6 produtos
(4, 5, 'simples', 'Calzone Napolitano Tradicional 🥟', 'Massa de pizza napolitana recheada com mussarela, presunto, cogumelos', 'calzone-tradicional', 48.90, 43.90, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com mussarela, presunto, cogumelos, molho', 25, 1, 1, 26),
(4, 5, 'simples', 'Calzone Quattro Formaggi 🥟', 'Calzone recheado com quatro queijos', 'calzone-quattro-formaggi', 51.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com mussarela, gorgonzola, parmesão, provolone', 25, 1, 1, 27),
(4, 5, 'simples', 'Calzone Diavola 🥟', 'Calzone recheado com salame picante e mussarela', 'calzone-diavola', 50.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com salame picante, mussarela, pimenta', 25, 1, 0, 28),
(4, 5, 'simples', 'Calzone Fungi 🥟', 'Calzone recheado com mix de cogumelos e mussarela', 'calzone-fungi', 52.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com mix de cogumelos, mussarela, parmesão', 25, 1, 1, 29),
(4, 5, 'simples', 'Calzone Frango com Catupiry 🥟', 'Calzone recheado com frango e catupiry', 'calzone-frango-catupiry', 49.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com frango desfiado, catupiry', 25, 1, 0, 30),
(4, 5, 'simples', 'Calzone Nutella 🥟', 'Calzone doce recheado com Nutella', 'calzone-nutella', 45.90, NULL, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=300', 'Calzone recheado com Nutella', 20, 1, 1, 31),

-- BORDAS RECHEADAS (subcategoria 4) - 4 produtos
(4, 4, 'personalizavel', 'Pizza Margherita com Borda Catupiry 🧀', 'Pizza margherita com borda de catupiry', 'margherita-borda-catupiry', 64.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza margherita com borda de catupiry', 40, 1, 1, 32),
(4, 4, 'personalizavel', 'Pizza Diavola com Borda Cheddar 🟨', 'Pizza diavola com borda de cheddar', 'diavola-borda-cheddar', 68.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza diavola com borda de cheddar', 40, 1, 1, 33),
(4, 4, 'personalizavel', 'Pizza Quattro Formaggi com Borda Gorgonzola 🧀', 'Pizza quatro queijos com borda de gorgonzola', 'quattro-formaggi-borda-gorgonzola', 70.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza quatro queijos com borda de gorgonzola', 40, 1, 0, 34),
(4, 4, 'personalizavel', 'Pizza Fungi com Borda Catupiry 🍄', 'Pizza de cogumelos com borda de catupiry', 'fungi-borda-catupiry', 72.90, NULL, 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=300', 'Pizza de cogumelos com borda de catupiry', 40, 1, 1, 35);


-- =====================================================
-- LOJA 5: Hamburgueria do Zé (35 produtos)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- HAMBÚRGUERES SIMPLES (subcategoria 6) - 12 produtos
(5, 6, 'personalizavel', 'X-Tudo do Zé 🍔', 'Pão australiano, hambúrguer 150g, bacon, ovo, alface, tomate, queijo cheddar, milho', 'x-tudo-ze', 28.90, 25.90, 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=300', 'Pão australiano, hambúrguer 150g, bacon, ovo, alface, tomate, queijo cheddar, milho', 15, 1, 1, 1),
(5, 6, 'personalizavel', 'X-Bacon do Zé 🥓', 'Pão brioche, hambúrguer 150g, bacon crocante, queijo cheddar', 'x-bacon-ze', 24.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer 150g, bacon crocante, queijo cheddar', 12, 1, 1, 2),
(5, 6, 'personalizavel', 'X-Calabresa do Zé 🌶️', 'Pão australiano, hambúrguer 150g, calabresa, queijo mussarela, cebola', 'x-calabresa-ze', 25.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão australiano, hambúrguer 150g, calabresa, queijo mussarela, cebola roxa', 13, 1, 0, 3),
(5, 6, 'personalizavel', 'X-Frango do Zé 🐔', 'Pão de batata, filé de frango grelhado, alface, tomate, queijo mussarela', 'x-frango-ze', 26.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de batata, filé de frango grelhado, alface, tomate, queijo mussarela', 14, 1, 0, 4),
(5, 6, 'personalizavel', 'X-Egg do Zé 🥚', 'Pão australiano, hambúrguer 150g, ovo frito, queijo cheddar, bacon', 'x-egg-ze', 26.90, NULL, 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=300', 'Pão australiano, hambúrguer 150g, ovo frito, queijo cheddar, bacon', 13, 1, 0, 5),
(5, 6, 'personalizavel', 'X-Salada do Zé 🥗', 'Pão de hambúrguer, hambúrguer 150g, alface, tomate, cebola, queijo mussarela', 'x-salada-ze', 22.90, NULL, 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=300', 'Pão de hambúrguer, hambúrguer 150g, alface, tomate, cebola, queijo mussarela', 10, 1, 0, 6),
(5, 6, 'personalizavel', 'Hambúrguer Simples do Zé 🍔', 'Pão de hambúrguer, hambúrguer 150g, queijo mussarela', 'hamburguer-simples-ze', 19.90, 17.90, 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=300', 'Pão de hambúrguer, hambúrguer 150g, queijo mussarela', 8, 1, 1, 7),
(5, 6, 'personalizavel', 'Cheeseburger do Zé 🧀', 'Pão de hambúrguer, hambúrguer 150g, queijo cheddar', 'cheeseburger-ze', 21.90, NULL, 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=300', 'Pão de hambúrguer, hambúrguer 150g, queijo cheddar', 9, 1, 0, 8),
(5, 6, 'personalizavel', 'Hambúrguer de Costela 🥩', 'Pão australiano, hambúrguer de costela 180g, queijo cheddar, cebola caramelizada', 'hamburguer-costela-ze', 32.90, 28.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão australiano, hambúrguer de costela 180g, queijo cheddar, cebola caramelizada', 16, 1, 1, 9),
(5, 6, 'personalizavel', 'Hambúrguer de Picanha 🥩', 'Pão brioche, hambúrguer de picanha 180g, queijo prato, alface, tomate', 'hamburguer-picanha-ze', 34.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão brioche, hambúrguer de picanha 180g, queijo prato, alface, tomate', 16, 1, 1, 10),
(5, 6, 'personalizavel', 'Hambúrguer de Frango com Catupiry 🐔', 'Pão de batata, hambúrguer de frango 150g, catupiry, milho', 'hamburguer-frango-catupiry-ze', 28.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de batata, hambúrguer de frango 150g, catupiry, milho', 14, 1, 0, 11),
(5, 6, 'personalizavel', 'Hambúrguer Vegano do Zé 🌱', 'Pão integral, hambúrguer de grão de bico, alface, tomate, molho de ervas', 'hamburguer-vegano-ze', 27.90, NULL, 'https://images.unsplash.com/photo-1610440042657-612c34d95a59?w=300', 'Pão integral, hambúrguer de grão de bico, alface, tomate, molho de ervas', 12, 1, 1, 12),

-- HAMBÚRGUERES ARTESANAIS (subcategoria 7) - 8 produtos
(5, 7, 'personalizavel', 'Zé Burger Artesanal 👨‍🍳', 'Pão brioche artesanal, blend de carnes 180g, queijo cheddar, bacon caramelizado, onion rings', 'ze-burger-artesanal', 36.90, 32.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche artesanal, blend de carnes 180g, queijo cheddar, bacon caramelizado, onion rings, molho especial', 18, 1, 1, 13),
(5, 7, 'personalizavel', 'Smash Burger do Zé 🔨', 'Pão australiano, hambúrguer smash 120g (duplo), queijo cheddar, picles, molho da casa', 'smash-burger-ze', 31.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão australiano, hambúrguer smash 120g (duplo), queijo cheddar, picles, molho da casa', 15, 1, 1, 14),
(5, 7, 'personalizavel', 'Burger de Costela com Cheddar 🥩', 'Pão brioche, hambúrguer de costela 200g, cheddar cremoso, cebola caramelizada', 'burger-costela-cheddar-ze', 38.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer de costela 200g, cheddar cremoso, cebola caramelizada', 18, 1, 1, 15),
(5, 7, 'personalizavel', 'Burger de Picanha com Queijo Prato 🥩', 'Pão australiano, hambúrguer de picanha 200g, queijo prato, alface americana, tomate', 'burger-picanha-prato-ze', 39.90, 35.90, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão australiano, hambúrguer de picanha 200g, queijo prato, alface americana, tomate', 18, 1, 1, 16),
(5, 7, 'personalizavel', 'Burger de Chorizo Argentino 🇦🇷', 'Pão brioche, hambúrguer de chorizo 180g, queijo provolone, chimichurri', 'burger-chorizo-ze', 37.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer de chorizo 180g, queijo provolone, chimichurri', 17, 1, 0, 17),
(5, 7, 'personalizavel', 'Burger de Cordeiro com Hortelã 🐑', 'Pão de batata, hambúrguer de cordeiro 180g, queijo de cabra, hortelã, rúcula', 'burger-cordeiro-ze', 41.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de batata, hambúrguer de cordeiro 180g, queijo de cabra, hortelã, rúcula', 18, 1, 1, 18),
(5, 7, 'personalizavel', 'Burger Vegano Artesanal 🌱', 'Pão integral, hambúrguer de lentilha e quinoa, tofu, rúcula, tomate seco', 'burger-vegano-artesanal-ze', 32.90, NULL, 'https://images.unsplash.com/photo-1610440042657-612c34d95a59?w=300', 'Pão integral, hambúrguer de lentilha e quinoa, tofu, rúcula, tomate seco', 15, 1, 1, 19),
(5, 7, 'personalizavel', 'Burger de Frango com Pesto 🐔', 'Pão de batata, hambúrguer de frango com ervas 180g, molho pesto, queijo mussarela, tomate seco', 'burger-frango-pesto-ze', 34.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de batata, hambúrguer de frango com ervas 180g, molho pesto, queijo mussarela, tomate seco', 16, 1, 0, 20),

-- COMBOS (subcategoria 8) - 5 produtos
(5, 8, 'combo', 'Combo X-Tudo + Batata + Refri 🍟', 'X-Tudo, batata frita média, refrigerante 350ml', 'combo-x-tudo-ze', 42.90, 39.90, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'X-Tudo, batata frita média, refrigerante 350ml', 20, 1, 1, 21),
(5, 8, 'combo', 'Combo X-Bacon + Batata + Refri 🥓', 'X-Bacon, batata frita média, refrigerante 350ml', 'combo-x-bacon-ze', 38.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'X-Bacon, batata frita média, refrigerante 350ml', 18, 1, 1, 22),
(5, 8, 'combo', 'Combo Zé Burger + Batata + Refri 👨‍🍳', 'Zé Burger Artesanal, batata frita média, refrigerante 350ml', 'combo-ze-burger', 49.90, 45.90, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Zé Burger Artesanal, batata frita média, refrigerante 350ml', 22, 1, 1, 23),
(5, 8, 'combo', 'Combo Família para 2 pessoas 👨‍👩‍👧', '2 X-Tudos, 2 batatas fritas médias, 2 refrigerantes 350ml', 'combo-familia-ze', 79.90, 72.90, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', '2 X-Tudos, 2 batatas fritas médias, 2 refrigerantes 350ml', 30, 1, 1, 24),
(5, 8, 'combo', 'Combo Vegano 🌱', 'Hambúrguer vegano, batata frita, suco natural', 'combo-vegano-ze', 42.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Hambúrguer vegano, batata frita, suco natural', 18, 1, 0, 25),

-- BATATAS FRITAS (subcategoria 9) - 5 produtos
(5, 9, 'simples', 'Batata Frita Pequena 🍟', 'Batata frita crocante porção pequena', 'batata-pequena-ze', 12.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Batata frita crocante', 7, 1, 0, 26),
(5, 9, 'simples', 'Batata Frita Média 🍟', 'Batata frita crocante porção média', 'batata-media-ze', 16.90, 14.90, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Batata frita crocante', 8, 1, 1, 27),
(5, 9, 'simples', 'Batata Frita Grande 🍟', 'Batata frita crocante porção grande', 'batata-grande-ze', 21.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Batata frita crocante', 10, 1, 0, 28),
(5, 9, 'simples', 'Batata com Cheddar e Bacon 🧀🥓', 'Batata frita coberta com cheddar cremoso e bacon crocante', 'batata-cheddar-bacon-ze', 24.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Batata frita, cheddar cremoso, bacon crocante', 10, 1, 1, 29),
(5, 9, 'simples', 'Batata Rústica com Alecrim 🌿', 'Batata rústica com alecrim e sal grosso', 'batata-rustica-ze', 19.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Batata rústica, alecrim, sal grosso', 12, 1, 0, 30),

-- ADICIONAIS (subcategoria 10) - 5 produtos
(5, 10, 'simples', 'Adicional de Bacon 🥓', 'Porção extra de bacon crocante', 'adicional-bacon-ze', 4.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Bacon crocante', 2, 1, 0, 31),
(5, 10, 'simples', 'Adicional de Queijo Cheddar 🧀', 'Porção extra de queijo cheddar', 'adicional-cheddar-ze', 4.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Queijo cheddar', 2, 1, 0, 32),
(5, 10, 'simples', 'Adicional de Hambúrguer 🍔', 'Hambúrguer extra 150g', 'adicional-hamburguer-ze', 8.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Hambúrguer 150g', 3, 1, 0, 33),
(5, 10, 'simples', 'Adicional de Ovo 🥚', 'Ovo frito', 'adicional-ovo-ze', 3.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Ovo frito', 2, 1, 0, 34),
(5, 10, 'simples', 'Adicional de Calabresa 🌶️', 'Porção de calabresa', 'adicional-calabresa-ze', 4.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Calabresa', 2, 1, 0, 35);



-- =====================================================
-- LOJA 6: The Burger House (35 produtos)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- SMASH BURGERS (especialidade da casa) - 15 produtos
(6, 6, 'personalizavel', 'Smash Burger Clássico 🔨', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, picles, molho da casa', 'smash-classico', 26.90, 23.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, picles, molho da casa', 12, 1, 1, 1),
(6, 6, 'personalizavel', 'Double Smash Burger 🔨🔨', 'Pão brioche, 2 hambúrgueres smash 120g, 2x queijo cheddar, picles, molho da casa', 'double-smash', 34.90, 30.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, 2 hambúrgueres smash 120g, 2x queijo cheddar, picles, molho da casa', 15, 1, 1, 2),
(6, 6, 'personalizavel', 'Smash Burger com Bacon 🥓', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, bacon crocante, picles, molho da casa', 'smash-bacon', 29.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, bacon crocante, picles, molho da casa', 13, 1, 1, 3),
(6, 6, 'personalizavel', 'Smash Burger com Cebola Caramelizada 🧅', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, cebola caramelizada, molho da casa', 'smash-cebola', 28.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, cebola caramelizada, molho da casa', 13, 1, 0, 4),
(6, 6, 'personalizavel', 'Smash Burger com Ovo 🥚', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, ovo frito, molho da casa', 'smash-ovo', 28.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, ovo frito, molho da casa', 13, 1, 0, 5),
(6, 6, 'personalizavel', 'Smash Burger com Jalapeño 🔥', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, jalapeño em conserva, molho da casa', 'smash-jalapeno', 27.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, jalapeño, molho da casa', 12, 1, 0, 6),
(6, 6, 'personalizavel', 'Smash Burger de Costela 🥩', 'Pão brioche, hambúrguer smash de costela 120g, queijo cheddar, cebola caramelizada, molho barbecue', 'smash-costela', 32.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash de costela 120g, queijo cheddar, cebola caramelizada, molho barbecue', 14, 1, 1, 7),
(6, 6, 'personalizavel', 'Smash Burger de Picanha 🥩', 'Pão brioche, hambúrguer smash de picanha 120g, queijo prato, alface, tomate, molho especial', 'smash-picanha', 33.90, 29.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash de picanha 120g, queijo prato, alface, tomate, molho especial', 14, 1, 1, 8),
(6, 6, 'personalizavel', 'Smash Burger de Frango com Pesto 🐔', 'Pão de batata, smash de frango 120g, molho pesto, queijo mussarela, tomate seco', 'smash-frango-pesto', 30.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de batata, smash de frango 120g, molho pesto, queijo mussarela, tomate seco', 13, 1, 0, 9),
(6, 6, 'personalizavel', 'Smash Burger Vegano 🌱', 'Pão integral, smash de grão de bico e quinoa 120g, tofu, rúcula, tomate seco', 'smash-vegano', 29.90, NULL, 'https://images.unsplash.com/photo-1610440042657-612c34d95a59?w=300', 'Pão integral, smash de grão de bico e quinoa 120g, tofu, rúcula, tomate seco', 12, 1, 1, 10),
(6, 6, 'personalizavel', 'Smash Burger com Cream Cheese 🧀', 'Pão brioche, hambúrguer smash 120g, cream cheese, cebola roxa, rúcula', 'smash-cream-cheese', 29.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash 120g, cream cheese, cebola roxa, rúcula', 13, 1, 1, 11),
(6, 6, 'personalizavel', 'Smash Burger com Guacamole 🥑', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, guacamole, nachos', 'smash-guacamole', 31.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, guacamole, nachos', 14, 1, 1, 12),
(6, 6, 'personalizavel', 'Smash Burger com BBQ 🍖', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, cebola caramelizada, molho barbecue, bacon', 'smash-bbq', 30.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão brioche, hambúrguer smash 120g, queijo cheddar, cebola caramelizada, molho barbecue, bacon', 14, 1, 0, 13),
(6, 6, 'personalizavel', 'Smash Burger com Provolone 🧀', 'Pão brioche, hambúrguer smash 120g, queijo provolone, rúcula, tomate seco', 'smash-provolone', 30.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash 120g, queijo provolone, rúcula, tomate seco', 13, 1, 0, 14),
(6, 6, 'personalizavel', 'Smash Burger com Cheddar Duplo 🧀🧀', 'Pão brioche, hambúrguer smash 120g, 2x queijo cheddar, cebola caramelizada', 'smash-cheddar-duplo', 31.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer smash 120g, 2x queijo cheddar, cebola caramelizada', 13, 1, 0, 15),

-- HAMBÚRGUERES ARTESANAIS (subcategoria 7) - 8 produtos
(6, 7, 'personalizavel', 'Burger House Especial 🏠', 'Pão australiano, blend de carnes 200g, queijo cheddar, bacon crocante, onion rings, molho especial', 'burger-house-especial', 38.90, 34.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão australiano, blend de carnes 200g, queijo cheddar, bacon crocante, onion rings, molho especial', 18, 1, 1, 16),
(6, 7, 'personalizavel', 'Burger de Costela com Cebola Caramelizada 🥩', 'Pão brioche, hambúrguer de costela 200g, queijo cheddar, cebola caramelizada, molho barbecue', 'burger-costela-cebola', 39.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer de costela 200g, queijo cheddar, cebola caramelizada, molho barbecue', 18, 1, 1, 17),
(6, 7, 'personalizavel', 'Burger de Picanha com Alho 🥩', 'Pão australiano, hambúrguer de picanha 200g, queijo prato, alho frito, rúcula', 'burger-picanha-alho', 40.90, 36.90, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão australiano, hambúrguer de picanha 200g, queijo prato, alho frito, rúcula', 18, 1, 1, 18),
(6, 7, 'personalizavel', 'Burger de Chorizo com Chimichurri 🇦🇷', 'Pão brioche, hambúrguer de chorizo 200g, queijo provolone, chimichurri', 'burger-chorizo-chimichurri', 39.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer de chorizo 200g, queijo provolone, chimichurri', 17, 1, 0, 19),
(6, 7, 'personalizavel', 'Burger de Cordeiro com Hortelã 🐑', 'Pão de batata, hambúrguer de cordeiro 200g, queijo de cabra, hortelã, rúcula', 'burger-cordeiro-hortela', 43.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de batata, hambúrguer de cordeiro 200g, queijo de cabra, hortelã, rúcula', 18, 1, 1, 20),
(6, 7, 'personalizavel', 'Burger Vegano Artesanal 🌱', 'Pão integral, hambúrguer de lentilha e quinoa, tofu grelhado, rúcula, tomate seco, molho de ervas', 'burger-vegano-artesanal-house', 34.90, NULL, 'https://images.unsplash.com/photo-1610440042657-612c34d95a59?w=300', 'Pão integral, hambúrguer de lentilha e quinoa, tofu grelhado, rúcula, tomate seco, molho de ervas', 15, 1, 1, 21),
(6, 7, 'personalizavel', 'Burger de Frango com Cream Cheese 🐔', 'Pão de batata, hambúrguer de frango com ervas 200g, cream cheese, cebola roxa, rúcula', 'burger-frango-cream-cheese', 35.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de batata, hambúrguer de frango com ervas 200g, cream cheese, cebola roxa, rúcula', 16, 1, 0, 22),
(6, 7, 'personalizavel', 'Burger de Mignon com Gorgonzola 🥩', 'Pão brioche, hambúrguer de filé mignon 200g, queijo gorgonzola, cebola caramelizada', 'burger-mignon-gorgonzola', 45.90, 40.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer de filé mignon 200g, queijo gorgonzola, cebola caramelizada', 19, 1, 1, 23),

-- COMBOS (subcategoria 8) - 4 produtos
(6, 8, 'combo', 'Combo Smash Clássico 🍟', 'Smash Burger Clássico, batata frita média, refrigerante 350ml', 'combo-smash-classico', 42.90, 38.90, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Smash Burger Clássico, batata frita média, refrigerante 350ml', 18, 1, 1, 24),
(6, 8, 'combo', 'Combo Double Smash 🔨🔨', 'Double Smash Burger, batata frita grande, refrigerante 350ml', 'combo-double-smash', 52.90, 47.90, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Double Smash Burger, batata frita grande, refrigerante 350ml', 20, 1, 1, 25),
(6, 8, 'combo', 'Combo Burger House Especial 🏠', 'Burger House Especial, batata com cheddar e bacon, refrigerante 350ml', 'combo-house-especial', 58.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Burger House Especial, batata com cheddar e bacon, refrigerante 350ml', 22, 1, 1, 26),
(6, 8, 'combo', 'Combo Vegano House 🌱', 'Burger vegano artesanal, batata rústica, suco natural', 'combo-vegano-house', 49.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Burger vegano artesanal, batata rústica, suco natural', 20, 1, 0, 27),

-- BATATAS FRITAS (subcategoria 9) - 4 produtos
(6, 9, 'simples', 'Batata Frita House 🍟', 'Batata frita crocante temperada com sal e alecrim', 'batata-house', 16.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Batata frita crocante temperada', 8, 1, 0, 28),
(6, 9, 'simples', 'Batata com Cheddar e Bacon House 🧀🥓', 'Batata frita coberta com cheddar cremoso e bacon crocante', 'batata-cheddar-bacon-house', 24.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Batata frita, cheddar cremoso, bacon crocante', 10, 1, 1, 29),
(6, 9, 'simples', 'Batata Rústica com Parmesão 🧀', 'Batata rústica com parmesão e ervas finas', 'batata-rustica-parmesao', 21.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Batata rústica, parmesão, ervas finas', 10, 1, 0, 30),
(6, 9, 'simples', 'Onion Rings (Anéis de Cebola) 🧅', 'Anéis de cebola empanados crocantes', 'onion-rings', 19.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Anéis de cebola empanados', 8, 1, 1, 31),

-- ADICIONAIS (subcategoria 10) - 4 produtos
(6, 10, 'simples', 'Adicional de Hambúrguer Smash 🍔', 'Hambúrguer smash extra 120g', 'adicional-smash', 8.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Hambúrguer smash 120g', 3, 1, 0, 32),
(6, 10, 'simples', 'Adicional de Bacon Extra 🥓', 'Porção extra de bacon crocante', 'adicional-bacon-house', 4.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Bacon crocante', 2, 1, 0, 33),
(6, 10, 'simples', 'Adicional de Cheddar Extra 🧀', 'Porção extra de queijo cheddar', 'adicional-cheddar-house', 4.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Queijo cheddar', 2, 1, 0, 34),
(6, 10, 'simples', 'Adicional de Molho Especial 🥫', 'Molho especial da casa', 'adicional-molho-house', 2.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Molho especial', 1, 1, 0, 35);



-- =====================================================
-- LOJA 7: Burger Lab (35 produtos - experimentais)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- HAMBÚRGUERES EXPERIMENTAIS (subcategoria 7) - 25 produtos
(7, 7, 'personalizavel', 'Burger Lab #001 - Umami Bomb 💣', 'Pão preto de carvão ativado, blend de wagyu e angus 200g, queijo gouda defumado, cogumelos shiitake, cebola crispy, maionese trufada', 'burger-lab-001', 48.90, 42.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão preto de carvão ativado, blend wagyu/angus 200g, queijo gouda defumado, cogumelos shiitake, cebola crispy, maionese trufada', 20, 1, 1, 1),
(7, 7, 'personalizavel', 'Burger Lab #002 - Picante Inferno 🔥', 'Pão com pimenta, hambúrguer 200g com pimenta jalapeño, queijo pepper jack, bacon picante, molho de pimenta habanero, onion rings', 'burger-lab-002', 44.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão com pimenta, hambúrguer 200g com jalapeño, queijo pepper jack, bacon picante, molho habanero, onion rings', 19, 1, 1, 2),
(7, 7, 'personalizavel', 'Burger Lab #003 - Floresta Negra 🌲', 'Pão de beterraba, hambúrguer de cogumelos portobello 200g, queijo brie, rúcula, tomate confit, redução de balsâmico', 'burger-lab-003', 42.90, 38.90, 'https://images.unsplash.com/photo-1610440042657-612c34d95a59?w=300', 'Pão de beterraba, hambúrguer de cogumelos portobello 200g, queijo brie, rúcula, tomate confit, redução de balsâmico', 18, 1, 1, 3),
(7, 7, 'personalizavel', 'Burger Lab #004 - Costela BBQ 🍖', 'Pão australiano, hambúrguer de costela 200g, queijo cheddar, cebola caramelizada, molho barbecue artesanal, bacon em cubos', 'burger-lab-004', 46.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão australiano, hambúrguer de costela 200g, queijo cheddar, cebola caramelizada, molho barbecue artesanal, bacon em cubos', 19, 1, 1, 4),
(7, 7, 'personalizavel', 'Burger Lab #005 - Mediterrâneo 🏺', 'Pão de ervas finas, hambúrguer de cordeiro 200g, queijo de cabra, tomate seco, rúcula, azeitonas pretas, molho de hortelã', 'burger-lab-005', 49.90, 44.90, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de ervas finas, hambúrguer de cordeiro 200g, queijo de cabra, tomate seco, rúcula, azeitonas pretas, molho de hortelã', 20, 1, 1, 5),
(7, 7, 'personalizavel', 'Burger Lab #006 - Frango Thai 🇹🇭', 'Pão de leite, hambúrguer de frango com gengibre e capim limão 200g, cream cheese, cenoura em conserva, coentro, molho de amendoim', 'burger-lab-006', 41.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de leite, hambúrguer de frango com gengibre e capim limão 200g, cream cheese, cenoura em conserva, coentro, molho de amendoim', 18, 1, 0, 6),
(7, 7, 'personalizavel', 'Burger Lab #007 - Mexicano 🌮', 'Pão de milho, hambúrguer de carne com pimenta 200g, queijo cheddar, guacamole, jalapeño, nachos, molho chipotle', 'burger-lab-007', 45.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão de milho, hambúrguer de carne com pimenta 200g, queijo cheddar, guacamole, jalapeño, nachos, molho chipotle', 19, 1, 1, 7),
(7, 7, 'personalizavel', 'Burger Lab #008 - Blue Cheese 🧀', 'Pão brioche, hambúrguer angus 200g, queijo gorgonzola, pera caramelizada, nozes, rúcula, mel', 'burger-lab-008', 47.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer angus 200g, queijo gorgonzola, pera caramelizada, nozes, rúcula, mel', 19, 1, 1, 8),
(7, 7, 'personalizavel', 'Burger Lab #009 - Oriental 🥢', 'Pão de gergelim preto, hambúrguer de porco com missô 200g, kimchi, pepino em conserva, maionese de wasabi', 'burger-lab-009', 43.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão de gergelim preto, hambúrguer de porco com missô 200g, kimchi, pepino em conserva, maionese de wasabi', 18, 1, 0, 9),
(7, 7, 'personalizavel', 'Burger Lab #010 - Trufado Negro 🖤', 'Pão preto com tinta de lula, hambúrguer wagyu 200g, queijo brie, cogumelos trufados, rúcula, maionese trufada', 'burger-lab-010', 54.90, 48.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão preto com tinta de lula, hambúrguer wagyu 200g, queijo brie, cogumelos trufados, rúcula, maionese trufada', 22, 1, 1, 10),
(7, 7, 'personalizavel', 'Burger Lab #011 - Italiano 🇮🇹', 'Pão de ciabatta, hambúrguer de carne com manjericão 200g, queijo mussarela de búfala, tomate seco, pesto, rúcula', 'burger-lab-011', 46.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão de ciabatta, hambúrguer de carne com manjericão 200g, queijo mussarela de búfala, tomate seco, pesto, rúcula', 19, 1, 0, 11),
(7, 7, 'personalizavel', 'Burger Lab #012 - Brasileiro 🇧🇷', 'Pão de mandioca, hambúrguer de picanha 200g, queijo coalho, farofa de bacon, vinagrete, banana frita', 'burger-lab-012', 47.90, 42.90, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão de mandioca, hambúrguer de picanha 200g, queijo coalho, farofa de bacon, vinagrete, banana frita', 20, 1, 1, 12),
(7, 7, 'personalizavel', 'Burger Lab #013 - Curry Indiano 🍛', 'Pão naan, hambúrguer de carne com curry 200g, chutney de manga, iogurte temperado, cebola roxa', 'burger-lab-013', 44.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão naan, hambúrguer de carne com curry 200g, chutney de manga, iogurte temperado, cebola roxa', 18, 1, 0, 13),
(7, 7, 'personalizavel', 'Burger Lab #014 - Salmão 🐟', 'Pão de gergelim, hambúrguer de salmão fresco 200g, cream cheese, alcaparras, rúcula, molho de endro', 'burger-lab-014', 52.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão de gergelim, hambúrguer de salmão fresco 200g, cream cheese, alcaparras, rúcula, molho de endro', 20, 1, 1, 14),
(7, 7, 'personalizavel', 'Burger Lab #015 - Vegano Proteico 🌱', 'Pão integral, hambúrguer de lentilha e quinoa 200g, tofu defumado, rúcula, tomate confit, molho de ervas', 'burger-lab-015', 39.90, NULL, 'https://images.unsplash.com/photo-1610440042657-612c34d95a59?w=300', 'Pão integral, hambúrguer de lentilha e quinoa 200g, tofu defumado, rúcula, tomate confit, molho de ervas', 16, 1, 1, 15),
(7, 7, 'personalizavel', 'Burger Lab #016 - Cordeiro com Hortelã 🐑', 'Pão sírio, hambúrguer de cordeiro 200g, queijo de cabra, hortelã, pepino, iogurte grego', 'burger-lab-016', 48.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Pão sírio, hambúrguer de cordeiro 200g, queijo de cabra, hortelã, pepino, iogurte grego', 19, 1, 0, 16),
(7, 7, 'personalizavel', 'Burger Lab #017 - Porco Agridoce 🐷', 'Pão de batata, hambúrguer de porco com abacaxi 200g, queijo prato, cebola caramelizada, molho agridoce', 'burger-lab-017', 42.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão de batata, hambúrguer de porco com abacaxi 200g, queijo prato, cebola caramelizada, molho agridoce', 18, 1, 0, 17),
(7, 7, 'personalizavel', 'Burger Lab #018 - Alemão 🇩🇪', 'Pão de centeio, hambúrguer de carne com mostarda 200g, chucrute, salsicha bratwurst, queijo suíço', 'burger-lab-018', 45.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão de centeio, hambúrguer de carne com mostarda 200g, chucrute, salsicha bratwurst, queijo suíço', 19, 1, 1, 18),
(7, 7, 'personalizavel', 'Burger Lab #019 - Francês 🇫🇷', 'Pão brioche, hambúrguer de carne com ervas de provence 200g, queijo camembert, cogumelos, cebola caramelizada', 'burger-lab-019', 47.90, NULL, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=300', 'Pão brioche, hambúrguer de carne com ervas de provence 200g, queijo camembert, cogumelos, cebola caramelizada', 19, 1, 0, 19),
(7, 7, 'personalizavel', 'Burger Lab #020 - Espanhol 🇪🇸', 'Pão rústico, hambúrguer de carne com páprica 200g, chouriço, queijo manchego, piquillo, aioli', 'burger-lab-020', 46.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão rústico, hambúrguer de carne com páprica 200g, chouriço, queijo manchego, piquillo, aioli', 19, 1, 0, 20),

-- COMBOS EXPERIMENTAIS (subcategoria 8) - 5 produtos
(7, 8, 'combo', 'Combo Lab #001 - Umami Experience 🧪', 'Burger Lab #001, batata rústica trufada, refrigerante artesanal', 'combo-lab-001', 69.90, 62.90, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Burger Lab #001, batata rústica trufada, refrigerante artesanal', 25, 1, 1, 21),
(7, 8, 'combo', 'Combo Lab #005 - Mediterrâneo Experience 🏺', 'Burger Lab #005, batata com alecrim e parmesão, suco natural', 'combo-lab-005', 68.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Burger Lab #005, batata com alecrim e parmesão, suco natural', 24, 1, 1, 22),
(7, 8, 'combo', 'Combo Lab #010 - Trufado Experience 🖤', 'Burger Lab #010, batata frita com parmesão e trufa, refrigerante premium', 'combo-lab-010', 76.90, 68.90, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Burger Lab #010, batata frita com parmesão e trufa, refrigerante premium', 26, 1, 1, 23),
(7, 8, 'combo', 'Combo Lab #012 - Brasileiro Experience 🇧🇷', 'Burger Lab #012, mandioca frita, guaraná artesanal', 'combo-lab-012', 66.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Burger Lab #012, mandioca frita, guaraná artesanal', 23, 1, 0, 24),
(7, 8, 'combo', 'Combo Lab #015 - Vegano Experience 🌱', 'Burger Lab #015, batata doce frita, suco detox', 'combo-lab-015', 58.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Burger Lab #015, batata doce frita, suco detox', 21, 1, 1, 25),

-- BATATAS ESPECIAIS (subcategoria 9) - 5 produtos
(7, 9, 'simples', 'Batata Rústica Trufada 🍄', 'Batata rústica com azeite trufado e parmesão', 'batata-rustica-trufada', 24.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Batata rústica, azeite trufado, parmesão, alecrim', 10, 1, 1, 26),
(7, 9, 'simples', 'Batata Doce Frita com Maple 🍠', 'Batata doce frita com molho de maple e bacon', 'batata-doce-maple', 22.90, NULL, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300', 'Batata doce frita, molho de maple, bacon crocante', 10, 1, 1, 27),
(7, 9, 'simples', 'Mandioca Frita com Molho de Alho 🧄', 'Mandioca frita crocante com molho de alho', 'mandioca-frita', 19.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Mandioca frita, molho de alho', 10, 1, 0, 28),
(7, 9, 'simples', 'Onion Rings Crocantes 🧅', 'Anéis de cebola empanados com molho barbecue', 'onion-rings-lab', 21.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Anéis de cebola empanados, molho barbecue', 8, 1, 0, 29),
(7, 9, 'simples', 'Polenta Frita com Gorgonzola 🟨', 'Polenta frita crocante com molho de gorgonzola', 'polenta-frita', 23.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Polenta frita, molho de gorgonzola', 10, 1, 1, 30),

-- ADICIONAIS (subcategoria 10) - 5 produtos
(7, 10, 'simples', 'Adicional de Queijo Trufado 🧀', 'Queijo brie com trufa', 'adicional-queijo-trufado', 6.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Queijo brie com trufa', 2, 1, 0, 31),
(7, 10, 'simples', 'Adicional de Bacon Defumado 🥓', 'Bacon defumado artesanal', 'adicional-bacon-defumado', 5.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Bacon defumado', 2, 1, 0, 32),
(7, 10, 'simples', 'Adicional de Cogumelos Trufados 🍄', 'Mix de cogumelos com trufa', 'adicional-cogumelos-trufados', 7.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Mix de cogumelos com trufa', 2, 1, 0, 33),
(7, 10, 'simples', 'Adicional de Guacamole 🥑', 'Guacamole fresco', 'adicional-guacamole', 6.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Guacamole', 2, 1, 0, 34),
(7, 10, 'simples', 'Adicional de Molho Especial Lab 🧪', 'Molho secreto do laboratório', 'adicional-molho-lab', 3.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Molho especial', 1, 1, 0, 35);


-- =====================================================
-- LOJA 8: Sushi Hakai BH (35 produtos)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- COMBINADOS (subcategoria 12) - 8 produtos
(8, 12, 'combo', 'Combo Hakai 20 peças 🍱', '10 sushis variados, 5 sashimis, 5 hot rolls', 'combo-hakai-20', 69.90, 62.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '10 sushis variados (salmão, atum, kappa), 5 sashimis de salmão, 5 hot rolls', 25, 1, 1, 1),
(8, 12, 'combo', 'Combo Hakai 30 peças 🍱', '12 sushis, 8 sashimis, 10 hot rolls', 'combo-hakai-30', 89.90, 79.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '12 sushis variados, 8 sashimis de salmão, 10 hot rolls', 30, 1, 1, 2),
(8, 12, 'combo', 'Combo Hakai 50 peças 🍱', '20 sushis, 15 sashimis, 15 hot rolls', 'combo-hakai-50', 149.90, 129.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '20 sushis variados, 15 sashimis, 15 hot rolls', 40, 1, 1, 3),
(8, 12, 'combo', 'Combo Hakai Especial 🎌', '10 sushis especiais, 10 sashimis premium, 10 hot rolls especiais', 'combo-hakai-especial', 129.90, 109.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '10 sushis especiais (skin, salmão cream cheese), 10 sashimis premium, 10 hot rolls especiais', 35, 1, 1, 4),
(8, 12, 'combo', 'Combo Executivo (10 peças) 💼', '5 sushis, 5 hot rolls', 'combo-executivo-10', 39.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '5 sushis variados, 5 hot rolls', 15, 1, 0, 5),
(8, 12, 'combo', 'Combo Familiar 80 peças 👪', '30 sushis, 25 sashimis, 25 hot rolls', 'combo-familiar-80', 219.90, 199.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '30 sushis variados, 25 sashimis, 25 hot rolls', 50, 1, 1, 6),
(8, 12, 'combo', 'Combo Vegano 🌱', '20 sushis veganos (kappa, cenoura, pepino, abacate)', 'combo-vegano-hakai', 59.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Sushis veganos de pepino, cenoura, kappa, abacate', 20, 1, 1, 7),
(8, 12, 'combo', 'Combo Rodízio Virtual (40 peças) 🎡', 'Mix variado para 2 pessoas', 'combo-rodizio-40', 119.90, NULL, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', 'Sushis, sashimis, hot rolls, uramakis variados', 35, 1, 0, 8),

-- SUSHIS (subcategoria 13) - 10 produtos
(8, 13, 'simples', 'Sushi de Salmão (8 peças) 🍣', 'Niguiri de salmão fresco', 'sushi-salmao-8', 24.90, 22.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 niguiris de salmão', 12, 1, 1, 9),
(8, 13, 'simples', 'Sushi de Atum (8 peças) 🐟', 'Niguiri de atum fresco', 'sushi-atum-8', 28.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 niguiris de atum', 12, 1, 0, 10),
(8, 13, 'simples', 'Sushi de Salmão Cream Cheese (8 peças) 🍣', 'Niguiri de salmão com cream cheese', 'sushi-salmao-cream-cheese-8', 26.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 niguiris de salmão com cream cheese', 12, 1, 1, 11),
(8, 13, 'simples', 'Uramaki Filadélfia (8 peças) 🥑', 'Uramaki de salmão, cream cheese e pepino', 'uramaki-filadelfia-8', 29.90, 26.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 uramakis de salmão, cream cheese, pepino, gergelim', 15, 1, 1, 12),
(8, 13, 'simples', 'Uramaki Skin (8 peças) 🐟', 'Uramaki de pele de salmão, pepino e molho teriyaki', 'uramaki-skin-8', 31.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 uramakis de skin, pepino, molho teriyaki', 15, 1, 0, 13),
(8, 13, 'simples', 'Uramaki Califórnia (8 peças) 🦀', 'Uramaki de kani, pepino, manga e gergelim', 'uramaki-california-8', 27.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 uramakis de kani, pepino, manga, gergelim', 15, 1, 1, 14),
(8, 13, 'simples', 'Uramaki de Camarão (8 peças) 🦐', 'Uramaki de camarão empanado, cream cheese, pepino', 'uramaki-camarao-8', 32.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 uramakis de camarão empanado, cream cheese, pepino', 15, 1, 0, 15),
(8, 13, 'simples', 'Uramaki Vegano (8 peças) 🌱', 'Uramaki de pepino, cenoura, abacate e gergelim', 'uramaki-vegano-8', 23.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 uramakis de pepino, cenoura, abacate, gergelim', 15, 1, 1, 16),
(8, 13, 'simples', 'Hossomaki (12 peças) 🍣', 'Rolinho fino de pepino ou salmão', 'hossomaki-12', 22.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '12 hossomakis de pepino ou salmão', 12, 1, 0, 17),
(8, 13, 'simples', 'Sushi Especial (8 peças) ✨', 'Sushis especiais da casa', 'sushi-especial-8', 34.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 sushis especiais com salmão, atum e cream cheese', 15, 1, 1, 18),

-- SASHIMIS (subcategoria 14) - 5 produtos
(8, 14, 'simples', 'Sashimi de Salmão (10 fatias) 🐟', 'Fatias de salmão fresco', 'sashimi-salmao-10', 34.90, 29.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '10 fatias de salmão fresco', 10, 1, 1, 19),
(8, 14, 'simples', 'Sashimi de Atum (10 fatias) 🐟', 'Fatias de atum fresco', 'sashimi-atum-10', 39.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '10 fatias de atum fresco', 10, 1, 0, 20),
(8, 14, 'simples', 'Sashimi Misto (15 fatias) 🐟', 'Mix de salmão e atum', 'sashimi-misto-15', 48.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 fatias de salmão, 7 fatias de atum', 12, 1, 1, 21),
(8, 14, 'simples', 'Sashimi de Salmão (20 fatias) 🐟', 'Porção grande de salmão', 'sashimi-salmao-20', 64.90, 59.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '20 fatias de salmão fresco', 15, 1, 1, 22),
(8, 14, 'simples', 'Sashimi Premium (10 fatias) 👑', 'Fatias especiais de salmão e atum', 'sashimi-premium-10', 44.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '5 fatias de salmão premium, 5 fatias de atum', 12, 1, 0, 23),

-- TEMAKIS (subcategoria 15) - 5 produtos
(8, 15, 'personalizavel', 'Temaki Salmão 🍥', 'Cone de alga com arroz, salmão e cream cheese', 'temaki-salmao', 22.90, 19.90, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, salmão fresco, cream cheese', 8, 1, 1, 24),
(8, 15, 'personalizavel', 'Temaki Skin 🐟', 'Cone de alga com arroz, skin e pepino', 'temaki-skin', 23.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, skin de salmão, pepino, molho teriyaki', 8, 1, 1, 25),
(8, 15, 'personalizavel', 'Temaki Califórnia 🦀', 'Cone de alga com arroz, kani, manga e pepino', 'temaki-california', 21.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, kani, manga, pepino', 8, 1, 0, 26),
(8, 15, 'personalizavel', 'Temaki de Camarão 🦐', 'Cone de alga com arroz, camarão empanado e cream cheese', 'temaki-camarao', 24.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, camarão empanado, cream cheese', 8, 1, 1, 27),
(8, 15, 'personalizavel', 'Temaki Vegano 🌱', 'Cone de alga com arroz, pepino, cenoura e abacate', 'temaki-vegano', 19.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, pepino, cenoura, abacate', 8, 1, 0, 28),

-- HOT ROLLS (subcategoria 16) - 4 produtos
(8, 16, 'simples', 'Hot Roll Salmão (8 unidades) 🔥', 'Hot roll empanado de salmão e cream cheese', 'hot-roll-salmao-8', 32.90, 28.90, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '8 hot rolls de salmão e cream cheese, empanados', 15, 1, 1, 29),
(8, 16, 'simples', 'Hot Roll Filadélfia (8 unidades) 🔥', 'Hot roll de salmão, cream cheese e pepino', 'hot-roll-filadelfia-8', 34.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '8 hot rolls de salmão, cream cheese, pepino', 15, 1, 1, 30),
(8, 16, 'simples', 'Hot Roll de Camarão (8 unidades) 🔥', 'Hot roll de camarão e cream cheese', 'hot-roll-camarao-8', 36.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '8 hot rolls de camarão e cream cheese', 15, 1, 0, 31),
(8, 16, 'simples', 'Hot Roll Especial (8 unidades) 🔥', 'Hot roll com salmão, cream cheese e cebolinha', 'hot-roll-especial-8', 35.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '8 hot rolls especiais', 15, 1, 1, 32),

-- ENTRADAS (subcategoria 17) - 3 produtos
(8, 17, 'simples', 'Sunomono 🥒', 'Pepino japonês em conserva', 'sunomono', 12.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Pepino japonês, vinagre de arroz, gergelim', 5, 1, 0, 33),
(8, 17, 'simples', 'Guioza (6 unidades) 🥟', 'Pastel japonês frito de carne ou frango', 'guioza-6', 18.90, NULL, 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=300', '6 guiozas fritos, molho agridoce', 10, 1, 1, 34),
(8, 17, 'simples', 'Missoshiru 🍲', 'Sopa de missô com tofu e cebolinha', 'missoshiru', 8.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', 'Caldo de missô, tofu, cebolinha, algas', 5, 1, 0, 35);



-- =====================================================
-- LOJA 9: Kenko Sushi (35 produtos - delivery)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- COMBINADOS EXECUTIVOS (subcategoria 12) - 10 produtos
(9, 12, 'combo', 'Combo Kenko 15 peças 🍱', '8 sushis, 4 sashimis, 3 hot rolls', 'combo-kenko-15', 42.90, 37.90, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '8 sushis variados, 4 sashimis de salmão, 3 hot rolls', 20, 1, 1, 1),
(9, 12, 'combo', 'Combo Kenko 25 peças 🍱', '12 sushis, 8 sashimis, 5 hot rolls', 'combo-kenko-25', 64.90, 57.90, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '12 sushis variados, 8 sashimis, 5 hot rolls', 25, 1, 1, 2),
(9, 12, 'combo', 'Combo Kenko 35 peças 🍱', '18 sushis, 10 sashimis, 7 hot rolls', 'combo-kenko-35', 86.90, 77.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '18 sushis, 10 sashimis, 7 hot rolls', 30, 1, 1, 3),
(9, 12, 'combo', 'Combo Kenko Executivo (12 peças) 💼', '6 sushis, 4 sashimis, 2 hot rolls', 'combo-kenko-executivo-12', 34.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 sushis, 4 sashimis, 2 hot rolls', 15, 1, 1, 4),
(9, 12, 'combo', 'Combo Kenko Plus (45 peças) ➕', '22 sushis, 13 sashimis, 10 hot rolls', 'combo-kenko-plus-45', 109.90, 98.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '22 sushis, 13 sashimis, 10 hot rolls', 35, 1, 1, 5),
(9, 12, 'combo', 'Combo Kenko Família (60 peças) 👪', '30 sushis, 15 sashimis, 15 hot rolls', 'combo-kenko-familia-60', 149.90, 134.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '30 sushis, 15 sashimis, 15 hot rolls', 40, 1, 1, 6),
(9, 12, 'combo', 'Combo Kenko Light (15 peças) 🥗', 'Sushis leves com pepino, kani e salmão', 'combo-kenko-light-15', 44.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', 'Sushis light com pepino, kani, salmão', 20, 1, 0, 7),
(9, 12, 'combo', 'Combo Kenko Vegano (20 peças) 🌱', 'Sushis veganos variados', 'combo-kenko-vegano-20', 49.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', 'Sushis veganos de pepino, cenoura, abacate, kappa', 20, 1, 1, 8),
(9, 12, 'combo', 'Combo Kenko Premium (30 peças) 👑', 'Sushis especiais e sashimis premium', 'combo-kenko-premium-30', 94.90, 84.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', 'Sushis especiais, sashimis premium', 30, 1, 1, 9),
(9, 12, 'combo', 'Combo Kenko Mega (80 peças) 🎯', 'O maior combo para até 4 pessoas', 'combo-kenko-mega-80', 229.90, 199.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '40 sushis, 20 sashimis, 20 hot rolls', 50, 1, 1, 10),

-- SUSHIS POPULARES (subcategoria 13) - 10 produtos
(9, 13, 'simples', 'Sushi Salmão (6 peças) 🍣', 'Niguiri de salmão fresco', 'sushi-salmao-6-kenko', 16.90, 14.90, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 niguiris de salmão', 10, 1, 1, 11),
(9, 13, 'simples', 'Sushi Atum (6 peças) 🐟', 'Niguiri de atum fresco', 'sushi-atum-6-kenko', 19.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 niguiris de atum', 10, 1, 0, 12),
(9, 13, 'simples', 'Sushi Kani (6 peças) 🦀', 'Niguiri de kani', 'sushi-kani-6-kenko', 14.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 niguiris de kani', 10, 1, 0, 13),
(9, 13, 'simples', 'Sushi Pepino (6 peças) 🥒', 'Niguiri de pepino (kappa)', 'sushi-pepino-6-kenko', 12.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 niguiris de pepino', 10, 1, 0, 14),
(9, 13, 'simples', 'Uramaki Filadélfia (6 peças) 🥑', 'Uramaki de salmão e cream cheese', 'uramaki-filadelfia-6-kenko', 21.90, 18.90, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 uramakis de salmão, cream cheese, pepino', 12, 1, 1, 15),
(9, 13, 'simples', 'Uramaki Skin (6 peças) 🐟', 'Uramaki de pele de salmão', 'uramaki-skin-6-kenko', 22.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 uramakis de skin, pepino, molho teriyaki', 12, 1, 1, 16),
(9, 13, 'simples', 'Uramaki Califórnia (6 peças) 🦀', 'Uramaki de kani e manga', 'uramaki-california-6-kenko', 19.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 uramakis de kani, manga, pepino', 12, 1, 0, 17),
(9, 13, 'simples', 'Uramaki Camarão (6 peças) 🦐', 'Uramaki de camarão empanado', 'uramaki-camarao-6-kenko', 24.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 uramakis de camarão empanado, cream cheese', 12, 1, 1, 18),
(9, 13, 'simples', 'Uramaki Vegano (6 peças) 🌱', 'Uramaki de legumes', 'uramaki-vegano-6-kenko', 16.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '6 uramakis de pepino, cenoura, abacate', 12, 1, 0, 19),
(9, 13, 'simples', 'Hossomaki Salmão (8 peças) 🍣', 'Rolinho fino de salmão', 'hossomaki-salmao-8-kenko', 18.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '8 hossomakis de salmão', 10, 1, 0, 20),

-- SASHIMIS (subcategoria 14) - 5 produtos
(9, 14, 'simples', 'Sashimi Salmão (8 fatias) 🐟', 'Fatias de salmão fresco', 'sashimi-salmao-8-kenko', 26.90, 23.90, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '8 fatias de salmão fresco', 8, 1, 1, 21),
(9, 14, 'simples', 'Sashimi Salmão (12 fatias) 🐟', 'Porção maior de salmão', 'sashimi-salmao-12-kenko', 36.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '12 fatias de salmão fresco', 10, 1, 1, 22),
(9, 14, 'simples', 'Sashimi Atum (8 fatias) 🐟', 'Fatias de atum fresco', 'sashimi-atum-8-kenko', 32.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '8 fatias de atum fresco', 8, 1, 0, 23),
(9, 14, 'simples', 'Sashimi Misto (12 fatias) 🐟', 'Mix de salmão e atum', 'sashimi-misto-12-kenko', 42.90, 37.90, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '8 fatias de salmão, 4 fatias de atum', 10, 1, 1, 24),
(9, 14, 'simples', 'Sashimi Especial (10 fatias) ✨', 'Fatias premium de salmão', 'sashimi-especial-10-kenko', 38.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', '10 fatias de salmão premium', 10, 1, 0, 25),

-- TEMAKIS (subcategoria 15) - 4 produtos
(9, 15, 'personalizavel', 'Temaki Salmão Kenko 🍥', 'Cone de alga com salmão e cream cheese', 'temaki-salmao-kenko', 19.90, 17.90, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', 'Alga, arroz, salmão, cream cheese', 7, 1, 1, 26),
(9, 15, 'personalizavel', 'Temaki Skin Kenko 🐟', 'Cone de alga com skin', 'temaki-skin-kenko', 20.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', 'Alga, arroz, skin, pepino', 7, 1, 1, 27),
(9, 15, 'personalizavel', 'Temaki Califórnia Kenko 🦀', 'Cone de alga com kani', 'temaki-california-kenko', 18.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', 'Alga, arroz, kani, manga', 7, 1, 0, 28),
(9, 15, 'personalizavel', 'Temaki Vegano Kenko 🌱', 'Cone de alga vegano', 'temaki-vegano-kenko', 16.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', 'Alga, arroz, pepino, cenoura, abacate', 7, 1, 0, 29),

-- HOT ROLLS (subcategoria 16) - 4 produtos
(9, 16, 'simples', 'Hot Roll Salmão (6 unidades) 🔥', 'Hot roll de salmão e cream cheese', 'hot-roll-salmao-6-kenko', 24.90, 21.90, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de salmão e cream cheese', 12, 1, 1, 30),
(9, 16, 'simples', 'Hot Roll Filadélfia (6 unidades) 🔥', 'Hot roll especial', 'hot-roll-filadelfia-6-kenko', 26.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de salmão, cream cheese, pepino', 12, 1, 1, 31),
(9, 16, 'simples', 'Hot Roll Camarão (6 unidades) 🔥', 'Hot roll de camarão', 'hot-roll-camarao-6-kenko', 28.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de camarão e cream cheese', 12, 1, 0, 32),
(9, 16, 'simples', 'Hot Roll Vegano (6 unidades) 🌱', 'Hot roll vegano', 'hot-roll-vegano-6-kenko', 22.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de legumes', 12, 1, 0, 33),

-- ENTRADAS (subcategoria 17) - 2 produtos
(9, 17, 'simples', 'Sunomono Kenko 🥒', 'Pepino em conserva', 'sunomono-kenko', 10.90, NULL, 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=300', 'Pepino japonês, vinagre de arroz', 5, 1, 0, 34),
(9, 17, 'simples', 'Guioza Kenko (6 unidades) 🥟', 'Pastel japonês', 'guioza-kenko-6', 16.90, NULL, 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=300', '6 guiozas de carne', 10, 1, 1, 35);



-- =====================================================
-- LOJA 10: Temaki House (35 produtos - temakis gigantes)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- TEMAKIS TRADICIONAIS (subcategoria 15) - 15 produtos
(10, 15, 'personalizavel', 'Temaki Salmão Tradicional 🍥', 'Cone gigante de alga com arroz, salmão fresco e cream cheese', 'temaki-salmao-tradicional', 24.90, 21.90, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz japonês, salmão fresco, cream cheese, cebolinha', 8, 1, 1, 1),
(10, 15, 'personalizavel', 'Temaki Skin Especial 🐟', 'Cone com pele de salmão crocante, pepino e molho teriyaki', 'temaki-skin-especial', 25.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, skin crocante, pepino, molho teriyaki, gergelim', 8, 1, 1, 2),
(10, 15, 'personalizavel', 'Temaki Califórnia 🦀', 'Cone com kani, manga, pepino e cream cheese', 'temaki-california-house', 23.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, kani, manga, pepino, cream cheese', 8, 1, 1, 3),
(10, 15, 'personalizavel', 'Temaki de Camarão Empanado 🦐', 'Cone com camarão empanado, cream cheese e alface', 'temaki-camarao-empanado', 27.90, 24.90, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, camarão empanado, cream cheese, alface', 9, 1, 1, 4),
(10, 15, 'personalizavel', 'Temaki de Atum Fresh 🐟', 'Cone com atum fresco, cebolinha e gergelim', 'temaki-atum-fresh', 26.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, atum fresco, cebolinha, gergelim', 8, 1, 0, 5),
(10, 15, 'personalizavel', 'Temaki Salmão com Cream Cheese 🍣', 'Cone generoso de salmão e cream cheese', 'temaki-salmao-cream-cheese', 25.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, salmão, cream cheese', 8, 1, 1, 6),
(10, 15, 'personalizavel', 'Temaki Salmão Grelhado 🔥', 'Cone com salmão grelhado e molho teriyaki', 'temaki-salmao-grelhado', 26.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, salmão grelhado, molho teriyaki, cebolinha', 9, 1, 1, 7),
(10, 15, 'personalizavel', 'Temaki Ebiten 🦐', 'Cone com camarão empanado, pepino e maionese', 'temaki-ebiten', 26.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, camarão empanado, pepino, maionese', 9, 1, 0, 8),
(10, 15, 'personalizavel', 'Temaki Kani com Cream Cheese 🦀', 'Cone com kani desfiado e cream cheese', 'temaki-kani-cream-cheese', 22.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, kani desfiado, cream cheese, cebolinha', 8, 1, 0, 9),
(10, 15, 'personalizavel', 'Temaki de Salmão e Pepino 🥒', 'Cone leve com salmão e pepino', 'temaki-salmao-pepino', 23.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, salmão, pepino, gergelim', 8, 1, 0, 10),
(10, 15, 'personalizavel', 'Temaki de Salmão e Abacate 🥑', 'Cone cremoso com salmão e abacate', 'temaki-salmao-abacate', 26.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, salmão, abacate, cream cheese', 8, 1, 1, 11),
(10, 15, 'personalizavel', 'Temaki de Kani e Abacate 🦀', 'Cone com kani e abacate', 'temaki-kani-abacate', 23.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, kani, abacate, pepino', 8, 1, 0, 12),
(10, 15, 'personalizavel', 'Temaki Vegano Especial 🌱', 'Cone com legumes frescos e molho especial', 'temaki-vegano-especial', 21.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, pepino, cenoura, abacate, molho de gergelim', 8, 1, 1, 13),
(10, 15, 'personalizavel', 'Temaki de Salmão e Cream Cheese com Cebolinha 🍣', 'Cone tradicional com toque de cebolinha', 'temaki-salmao-cebolinha', 24.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, salmão, cream cheese, cebolinha', 8, 1, 0, 14),
(10, 15, 'personalizavel', 'Temaki Duplo Salmão e Atum 🍥', 'Cone com dois peixes nobres', 'temaki-duplo-salmao-atum', 29.90, 26.90, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga, arroz, salmão, atum, cream cheese', 9, 1, 1, 15),

-- TEMAKIS GIGANTES (subcategoria 15) - 8 produtos (versões maiores)
(10, 15, 'personalizavel', 'Temaki Gigante Salmão 🍥', 'Temaki tamanho XXL de salmão e cream cheese', 'temaki-gigante-salmao', 34.90, 29.90, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga GG, arroz, salmão em dobro, cream cheese, cebolinha', 12, 1, 1, 16),
(10, 15, 'personalizavel', 'Temaki Gigante Skin 🐟', 'Temaki tamanho XXL de skin', 'temaki-gigante-skin', 35.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga GG, arroz, skin em dobro, pepino, teriyaki', 12, 1, 1, 17),
(10, 15, 'personalizavel', 'Temaki Gigante Camarão 🦐', 'Temaki tamanho XXL de camarão empanado', 'temaki-gigante-camarao', 37.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga GG, arroz, camarão empanado em dobro, cream cheese', 13, 1, 1, 18),
(10, 15, 'personalizavel', 'Temaki Gigante Califórnia 🦀', 'Temaki tamanho XXL de kani', 'temaki-gigante-california', 32.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga GG, arroz, kani, manga, pepino, cream cheese', 12, 1, 0, 19),
(10, 15, 'personalizavel', 'Temaki Gigante Atum 🐟', 'Temaki tamanho XXL de atum', 'temaki-gigante-atum', 36.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga GG, arroz, atum em dobro, cebolinha', 12, 1, 0, 20),
(10, 15, 'personalizavel', 'Temaki Gigante Salmão Grelhado 🔥', 'Temaki tamanho XXL de salmão grelhado', 'temaki-gigante-salmao-grelhado', 36.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga GG, arroz, salmão grelhado em dobro, teriyaki', 13, 1, 1, 21),
(10, 15, 'personalizavel', 'Temaki Gigante Vegano 🌱', 'Temaki tamanho XXL de legumes', 'temaki-gigante-vegano', 29.90, NULL, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga GG, arroz, pepino, cenoura, abacate, molho especial', 12, 1, 0, 22),
(10, 15, 'personalizavel', 'Temaki Gigante Especial da Casa 👑', 'Temaki XXL com salmão, atum, camarão e cream cheese', 'temaki-gigante-especial', 42.90, 37.90, 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=300', 'Alga GG, arroz, salmão, atum, camarão, cream cheese, cebolinha', 15, 1, 1, 23),

-- COMBOS DE TEMAKIS (subcategoria 12) - 5 produtos
(10, 12, 'combo', 'Combo 2 Temakis Tradicionais 🍥', 'Escolha 2 temakis da linha tradicional', 'combo-2-temakis', 44.90, 39.90, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '2 temakis à sua escolha', 15, 1, 1, 24),
(10, 12, 'combo', 'Combo 3 Temakis Gigantes 🍥', 'Escolha 3 temakis gigantes', 'combo-3-temakis-gigantes', 89.90, 79.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '3 temakis gigantes à sua escolha', 25, 1, 1, 25),
(10, 12, 'combo', 'Combo Temaki + Hot Roll 🔥', '1 temaki tradicional + 4 hot rolls', 'combo-temaki-hot', 39.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '1 temaki + 4 hot rolls', 15, 1, 0, 26),
(10, 12, 'combo', 'Combo Família Temakis 👪', '4 temakis tradicionais + 2 temakis gigantes', 'combo-familia-temakis', 139.90, 119.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '4 temakis tradicionais, 2 temakis gigantes', 30, 1, 1, 27),
(10, 12, 'combo', 'Combo Degustação Temakis 🍣', '3 temakis pequenos para degustação', 'combo-degustacao-temakis', 42.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '3 temakis mini de sabores variados', 15, 1, 0, 28),

-- HOT ROLLS (subcategoria 16) - 4 produtos
(10, 16, 'simples', 'Hot Roll Salmão (6 unidades) 🔥', 'Hot roll de salmão e cream cheese', 'hot-roll-salmao-6-house', 28.90, 24.90, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de salmão e cream cheese', 12, 1, 1, 29),
(10, 16, 'simples', 'Hot Roll Filadélfia (6 unidades) 🔥', 'Hot roll de salmão, cream cheese e pepino', 'hot-roll-filadelfia-6-house', 29.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de salmão, cream cheese, pepino', 12, 1, 1, 30),
(10, 16, 'simples', 'Hot Roll Camarão (6 unidades) 🔥', 'Hot roll de camarão e cream cheese', 'hot-roll-camarao-6-house', 31.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de camarão e cream cheese', 12, 1, 0, 31),
(10, 16, 'simples', 'Hot Roll Especial (6 unidades) 🔥', 'Hot roll com salmão, atum e cream cheese', 'hot-roll-especial-6-house', 32.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls especiais', 12, 1, 1, 32),

-- SUSHIS (subcategoria 13) - 3 produtos
(10, 13, 'simples', 'Sushi Salmão (8 peças) 🍣', 'Niguiri de salmão fresco', 'sushi-salmao-8-house', 22.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 niguiris de salmão', 10, 1, 0, 33),
(10, 13, 'simples', 'Uramaki Filadélfia (8 peças) 🥑', 'Uramaki de salmão e cream cheese', 'uramaki-filadelfia-8-house', 28.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 uramakis de salmão, cream cheese, pepino', 12, 1, 1, 34),
(10, 13, 'simples', 'Uramaki Califórnia (8 peças) 🦀', 'Uramaki de kani e manga', 'uramaki-california-8-house', 26.90, NULL, 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=300', '8 uramakis de kani, manga, pepino', 12, 1, 0, 35);




-- =====================================================
-- LOJA 11: Sushi da Hora (35 produtos - preços populares)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- COMBINADOS PROMOCIONAIS (subcategoria 12) - 10 produtos
(11, 12, 'combo', 'Combo Hora 15 peças 🍱', '8 sushis, 4 sashimis, 3 hot rolls - preço especial', 'combo-hora-15', 29.90, 24.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '8 sushis variados, 4 sashimis, 3 hot rolls', 20, 1, 1, 1),
(11, 12, 'combo', 'Combo Hora 25 peças 🍱', '12 sushis, 8 sashimis, 5 hot rolls - super promoção', 'combo-hora-25', 44.90, 39.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '12 sushis, 8 sashimis, 5 hot rolls', 25, 1, 1, 2),
(11, 12, 'combo', 'Combo Hora 35 peças 🍱', '18 sushis, 10 sashimis, 7 hot rolls - melhor custo-benefício', 'combo-hora-35', 59.90, 49.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '18 sushis, 10 sashimis, 7 hot rolls', 30, 1, 1, 3),
(11, 12, 'combo', 'Combo Hora Executivo (10 peças) 💼', '6 sushis, 4 sashimis - rápido e barato', 'combo-hora-executivo-10', 19.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 sushis, 4 sashimis', 12, 1, 1, 4),
(11, 12, 'combo', 'Combo Hora Família (50 peças) 👪', '25 sushis, 15 sashimis, 10 hot rolls', 'combo-hora-familia-50', 89.90, 79.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '25 sushis, 15 sashimis, 10 hot rolls', 35, 1, 1, 5),
(11, 12, 'combo', 'Combo Hora Light (12 peças) 🥗', 'Sushis leves com pepino e kani', 'combo-hora-light-12', 24.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', 'Sushis light com pepino, kani', 15, 1, 0, 6),
(11, 12, 'combo', 'Combo Hora Vegano (15 peças) 🌱', 'Sushis veganos', 'combo-hora-vegano-15', 27.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', 'Sushis veganos variados', 15, 1, 1, 7),
(11, 12, 'combo', 'Combo Hora Happy Hour (20 peças) 🍻', 'Promoção de final de tarde', 'combo-hora-happy-20', 34.90, 29.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '10 sushis, 6 sashimis, 4 hot rolls', 20, 1, 1, 8),
(11, 12, 'combo', 'Combo Hora Mega (70 peças) 🎯', 'O maior combo popular', 'combo-hora-mega-70', 129.90, 109.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '35 sushis, 20 sashimis, 15 hot rolls', 45, 1, 1, 9),
(11, 12, 'combo', 'Combo Hora Duplo (40 peças) 👥', 'Para 2 pessoas', 'combo-hora-duplo-40', 69.90, 59.90, 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=300', '20 sushis, 12 sashimis, 8 hot rolls', 30, 1, 1, 10),

-- SUSHIS POPULARES (subcategoria 13) - 10 produtos
(11, 13, 'simples', 'Sushi Salmão (6 peças) 🍣', 'Niguiri de salmão - preço popular', 'sushi-salmao-6-hora', 12.90, 9.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 niguiris de salmão', 8, 1, 1, 11),
(11, 13, 'simples', 'Sushi Salmão (10 peças) 🍣', 'Niguiri de salmão - porção família', 'sushi-salmao-10-hora', 19.90, 16.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '10 niguiris de salmão', 12, 1, 1, 12),
(11, 13, 'simples', 'Sushi Kani (6 peças) 🦀', 'Niguiri de kani', 'sushi-kani-6-hora', 10.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 niguiris de kani', 8, 1, 0, 13),
(11, 13, 'simples', 'Sushi Pepino (6 peças) 🥒', 'Niguiri de pepino (kappa)', 'sushi-pepino-6-hora', 8.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 niguiris de pepino', 8, 1, 0, 14),
(11, 13, 'simples', 'Uramaki Filadélfia (6 peças) 🥑', 'Uramaki popular de salmão', 'uramaki-filadelfia-6-hora', 15.90, 13.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 uramakis de salmão, cream cheese, pepino', 10, 1, 1, 15),
(11, 13, 'simples', 'Uramaki Skin (6 peças) 🐟', 'Uramaki de pele de salmão', 'uramaki-skin-6-hora', 16.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 uramakis de skin, pepino', 10, 1, 1, 16),
(11, 13, 'simples', 'Uramaki Califórnia (6 peças) 🦀', 'Uramaki de kani', 'uramaki-california-6-hora', 14.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 uramakis de kani, manga, pepino', 10, 1, 0, 17),
(11, 13, 'simples', 'Uramaki Camarão (6 peças) 🦐', 'Uramaki de camarão', 'uramaki-camarao-6-hora', 17.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 uramakis de camarão, cream cheese', 10, 1, 1, 18),
(11, 13, 'simples', 'Hossomaki Salmão (8 peças) 🍣', 'Rolinho fino de salmão', 'hossomaki-salmao-8-hora', 14.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '8 hossomakis de salmão', 8, 1, 0, 19),
(11, 13, 'simples', 'Hossomaki Pepino (8 peças) 🥒', 'Rolinho fino de pepino', 'hossomaki-pepino-8-hora', 9.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '8 hossomakis de pepino', 8, 1, 0, 20),

-- SASHIMIS POPULARES (subcategoria 14) - 5 produtos
(11, 14, 'simples', 'Sashimi Salmão (8 fatias) 🐟', 'Fatias de salmão - preço popular', 'sashimi-salmao-8-hora', 19.90, 16.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '8 fatias de salmão', 6, 1, 1, 21),
(11, 14, 'simples', 'Sashimi Salmão (12 fatias) 🐟', 'Porção maior de salmão', 'sashimi-salmao-12-hora', 27.90, 24.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '12 fatias de salmão', 8, 1, 1, 22),
(11, 14, 'simples', 'Sashimi Misto (10 fatias) 🐟', 'Mix de salmão e atum', 'sashimi-misto-10-hora', 29.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '6 fatias de salmão, 4 de atum', 7, 1, 1, 23),
(11, 14, 'simples', 'Sashimi Promocional (15 fatias) 🐟', 'Oferta especial do dia', 'sashimi-promo-15-hora', 34.90, 29.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '15 fatias de salmão', 10, 1, 1, 24),
(11, 14, 'simples', 'Sashimi de Atum (8 fatias) 🐟', 'Fatias de atum', 'sashimi-atum-8-hora', 26.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', '8 fatias de atum', 6, 1, 0, 25),

-- TEMAKIS POPULARES (subcategoria 15) - 4 produtos
(11, 15, 'personalizavel', 'Temaki Salmão Hora 🍥', 'Cone de salmão com cream cheese', 'temaki-salmao-hora', 16.90, 13.90, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', 'Alga, arroz, salmão, cream cheese', 6, 1, 1, 26),
(11, 15, 'personalizavel', 'Temaki Skin Hora 🐟', 'Cone de skin', 'temaki-skin-hora', 17.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', 'Alga, arroz, skin, pepino', 6, 1, 1, 27),
(11, 15, 'personalizavel', 'Temaki Califórnia Hora 🦀', 'Cone de kani', 'temaki-california-hora', 15.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', 'Alga, arroz, kani, manga', 6, 1, 0, 28),
(11, 15, 'personalizavel', 'Temaki Vegano Hora 🌱', 'Cone de legumes', 'temaki-vegano-hora', 14.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', 'Alga, arroz, pepino, cenoura', 6, 1, 0, 29),

-- HOT ROLLS POPULARES (subcategoria 16) - 4 produtos
(11, 16, 'simples', 'Hot Roll Salmão (4 unidades) 🔥', 'Hot roll de salmão', 'hot-roll-salmao-4-hora', 14.90, 12.90, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '4 hot rolls de salmão e cream cheese', 8, 1, 1, 30),
(11, 16, 'simples', 'Hot Roll Salmão (6 unidades) 🔥', 'Hot roll de salmão', 'hot-roll-salmao-6-hora', 19.90, 16.90, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de salmão e cream cheese', 10, 1, 1, 31),
(11, 16, 'simples', 'Hot Roll Filadélfia (6 unidades) 🔥', 'Hot roll especial', 'hot-roll-filadelfia-6-hora', 21.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '6 hot rolls de salmão, cream cheese, pepino', 10, 1, 0, 32),
(11, 16, 'simples', 'Hot Roll Camarão (4 unidades) 🔥', 'Hot roll de camarão', 'hot-roll-camarao-4-hora', 17.90, NULL, 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=300', '4 hot rolls de camarão e cream cheese', 8, 1, 0, 33),

-- ENTRADAS (subcategoria 17) - 2 produtos
(11, 17, 'simples', 'Sunomono Hora 🥒', 'Pepino em conserva', 'sunomono-hora', 6.90, NULL, 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=300', 'Pepino japonês', 4, 1, 0, 34),
(11, 17, 'simples', 'Guioza Hora (4 unidades) 🥟', 'Pastel japonês', 'guioza-hora-4', 9.90, NULL, 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=300', '4 guiozas de carne', 8, 1, 0, 35);



-- =====================================================
-- LOJA 12: Restaurante Sabor da Terra (35 produtos - comida mineira)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- PRATOS EXECUTIVOS (subcategoria 18) - 10 produtos
(12, 18, 'simples', 'Prato Executivo - Frango Grelhado 🍗', 'Filé de frango grelhado, arroz branco, feijão, salada, batata frita', 'executivo-frango-terra', 26.90, 23.90, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Filé de frango, arroz, feijão, salada, batata frita', 20, 1, 1, 1),
(12, 18, 'simples', 'Prato Executivo - Carne de Panela 🥩', 'Carne de panela, arroz, feijão, legumes, couve refogada', 'executivo-carne-terra', 28.90, 25.90, 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=300', 'Carne de panela, arroz, feijão, legumes, couve', 20, 1, 1, 2),
(12, 18, 'simples', 'Prato Executivo - Bife à Milanesa 🥩', 'Bife empanado, arroz, feijão, salada, fritas', 'executivo-bife-terra', 27.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Bife à milanesa, arroz, feijão, salada, batata frita', 18, 1, 0, 3),
(12, 18, 'simples', 'Prato Executivo - Peixe Grelhado 🐟', 'Filé de peixe grelhado, arroz, feijão, salada, legumes', 'executivo-peixe-terra', 29.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Filé de peixe, arroz, feijão, salada, legumes', 18, 1, 1, 4),
(12, 18, 'simples', 'Prato Executivo - Frango à Parmegiana 🍗', 'Frango à parmegiana, arroz, batata frita', 'executivo-parmegiana-terra', 31.90, 28.90, 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e8?w=300', 'Frango empanado, molho de tomate, mussarela, arroz, batata', 22, 1, 1, 5),
(12, 18, 'simples', 'Prato Executivo - Carne de Sol 🥩', 'Carne de sol desfiada, arroz, feijão, mandioca, vinagrete', 'executivo-carne-sol-terra', 32.90, NULL, 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=300', 'Carne de sol, arroz, feijão, mandioca, vinagrete', 22, 1, 1, 6),
(12, 18, 'simples', 'Prato Executivo - Costelinha Suína 🐷', 'Costelinha assada, arroz, feijão, salada', 'executivo-costelinha-terra', 30.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Costelinha suína, arroz, feijão, salada', 25, 1, 0, 7),
(12, 18, 'simples', 'Prato Executivo - Filé de Frango com Cheddar 🧀', 'Filé de frango, cheddar, arroz, feijão, fritas', 'executivo-frango-cheddar-terra', 29.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Frango, cheddar, arroz, feijão, batata', 20, 1, 0, 8),
(12, 18, 'simples', 'Prato Executivo - Bife de Fígado 🥩', 'Bife de fígado acebolado, arroz, feijão, salada', 'executivo-figado-terra', 26.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Fígado acebolado, arroz, feijão, salada', 18, 1, 0, 9),
(12, 18, 'simples', 'Prato Executivo - Vegetariano 🌱', 'Legumes salteados, arroz, feijão, salada, batata', 'executivo-vegetariano-terra', 25.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Legumes, arroz, feijão, salada, batata', 18, 1, 1, 10),

-- PF (PRATO FEITO) (subcategoria 19) - 8 produtos
(12, 19, 'simples', 'PF Básico - Arroz, Feijão, Bife e Ovo 🍳', 'Arroz, feijão, bife acebolado, ovo frito, salada simples', 'pf-basico-terra', 19.90, 17.90, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Arroz, feijão, bife, ovo, salada', 15, 1, 1, 11),
(12, 19, 'simples', 'PF Frango - Arroz, Feijão, Frango Grelhado 🍗', 'Arroz, feijão, frango grelhado, salada', 'pf-frango-terra', 21.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Arroz, feijão, frango, salada', 15, 1, 1, 12),
(12, 19, 'simples', 'PF Carne de Panela 🥩', 'Arroz, feijão, carne de panela, couve', 'pf-carne-panela-terra', 23.90, NULL, 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=300', 'Arroz, feijão, carne de panela, couve', 15, 1, 1, 13),
(12, 19, 'simples', 'PF Peixe - Arroz, Feijão, Peixe Frito 🐟', 'Arroz, feijão, filé de peixe frito, salada', 'pf-peixe-terra', 24.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Arroz, feijão, peixe frito, salada', 18, 1, 0, 14),
(12, 19, 'simples', 'PF Linguiça - Arroz, Feijão, Linguiça Frita 🌭', 'Arroz, feijão, linguiça frita, batata frita', 'pf-linguica-terra', 22.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Arroz, feijão, linguiça, batata', 15, 1, 0, 15),
(12, 19, 'simples', 'PF Misto - Arroz, Feijão, Bife e Frango 🥩🍗', 'Arroz, feijão, bife e frango, salada', 'pf-misto-terra', 27.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Arroz, feijão, bife, frango, salada', 20, 1, 1, 16),
(12, 19, 'simples', 'PF Vegetariano - Arroz, Feijão, Legumes 🌱', 'Arroz, feijão, legumes salteados, salada', 'pf-vegetariano-terra', 20.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Arroz, feijão, legumes, salada', 15, 1, 0, 17),
(12, 19, 'simples', 'PF Super - Arroz, Feijão, Bife, Ovo, Batata Frita e Salada 🍟', 'Completo com tudo', 'pf-super-terra', 28.90, 25.90, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Arroz, feijão, bife, ovo, batata, salada', 20, 1, 1, 18),

-- FEIJOADA (subcategoria 20) - 4 produtos
(12, 20, 'simples', 'Feijoada Individual (Quartas e Sábados) 🥘', 'Feijoada completa individual', 'feijoada-individual-terra', 34.90, 29.90, 'https://images.unsplash.com/photo-1626082927381-49cd97f1bfcc?w=300', 'Feijoada, arroz, couve, farofa, torresmo, laranja', 25, 1, 1, 19),
(12, 20, 'simples', 'Feijoada para 2 pessoas 🥘', 'Feijoada completa para 2', 'feijoada-2-terra', 64.90, 54.90, 'https://images.unsplash.com/photo-1626082927381-49cd97f1bfcc?w=300', 'Feijoada em dobro, arroz, couve, farofa, torresmo, laranja', 30, 1, 1, 20),
(12, 20, 'simples', 'Feijoada para 4 pessoas 🥘', 'Feijoada completa para 4', 'feijoada-4-terra', 119.90, 99.90, 'https://images.unsplash.com/photo-1626082927381-49cd97f1bfcc?w=300', 'Feijoada família, arroz, couve, farofa, torresmo, laranja', 40, 1, 1, 21),
(12, 20, 'simples', 'Feijoada Light 🥗', 'Feijoada com menos gordura', 'feijoada-light-terra', 32.90, NULL, 'https://images.unsplash.com/photo-1626082927381-49cd97f1bfcc?w=300', 'Feijoada light, arroz integral, couve refogada', 25, 1, 0, 22),

-- PETISCOS (subcategoria 21) - 6 produtos
(12, 21, 'simples', 'Porção de Batata Frita (300g) 🍟', 'Batata frita crocante', 'batata-300-terra', 16.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Batata frita, sal', 8, 1, 0, 23),
(12, 21, 'simples', 'Porção de Mandioca Frita (300g) 🥔', 'Mandioca frita crocante', 'mandioca-300-terra', 18.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Mandioca frita, sal grosso', 10, 1, 1, 24),
(12, 21, 'simples', 'Porção de Torresmo 🐷', 'Torresmo pururuca crocante', 'torresmo-terra', 19.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Torresmo pururuca, limão', 10, 1, 1, 25),
(12, 21, 'simples', 'Porção de Calabresa Acebolada 🌶️', 'Calabresa frita com cebola', 'calabresa-acebolada-terra', 22.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Calabresa, cebola, pimentão', 10, 1, 1, 26),
(12, 21, 'simples', 'Porção de Frango a Passarinho 🐔', 'Frango a passarinho crocante', 'frango-passarinho-terra', 24.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Frango a passarinho, alho, limão', 15, 1, 0, 27),
(12, 21, 'simples', 'Porção Mista (Batata, Mandioca e Torresmo) 🥔', 'Combo de petiscos', 'porcao-mista-terra', 34.90, 29.90, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Batata, mandioca, torresmo', 15, 1, 1, 28),

-- CARNES (subcategoria 22) - 7 produtos
(12, 22, 'simples', 'Picanha Grelhada (300g) 🥩', 'Picanha grelhada com acompanhamentos', 'picanha-300-terra', 49.90, 44.90, 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=300', 'Picanha, arroz, feijão, farofa, vinagrete', 25, 1, 1, 29),
(12, 22, 'simples', 'Filé Mignon (250g) 🥩', 'Filé mignon grelhado com acompanhamentos', 'file-mignon-terra', 52.90, NULL, 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=300', 'Filé mignon, arroz, feijão, batata', 25, 1, 1, 30),
(12, 22, 'simples', 'Costela no Bafo (500g) 🥩', 'Costela bovina assada lentamente', 'costela-bafo-terra', 54.90, 48.90, 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=300', 'Costela, arroz, feijão, couve', 30, 1, 1, 31),
(12, 22, 'simples', 'Frango Grelhado (400g) 🍗', 'Frango grelhado com acompanhamentos', 'frango-grelhado-terra', 36.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Frango, arroz, feijão, salada', 20, 1, 0, 32),
(12, 22, 'simples', 'Linguiça Artesanal (300g) 🌭', 'Linguiça artesanal grelhada', 'linguica-artesanal-terra', 32.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Linguiça, arroz, feijão, vinagrete', 18, 1, 0, 33),
(12, 22, 'simples', 'Coração de Frango (200g) ❤️', 'Coraçãozinho grelhado', 'coracao-frango-terra', 24.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Coração de frango, molho', 15, 1, 0, 34),
(12, 22, 'simples', 'Carne de Sol com Mandioca (400g) 🥩', 'Carne de sol acebolada com mandioca', 'carne-sol-mandioca-terra', 45.90, 40.90, 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=300', 'Carne de sol, mandioca, manteiga de garrafa', 20, 1, 1, 35);



-- =====================================================
-- LOJA 13: Cantina da Nonna (35 produtos - italiana)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- MASSAS (subcategoria 33) - 15 produtos
(13, 33, 'simples', 'Espaguete à Bolonhesa 🍝', 'Molho bolonhesa tradicional com carne moída e manjericão', 'espaguete-bolonhesa', 38.90, 34.90, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Espaguete, molho de tomate, carne moída, manjericão, parmesão', 20, 1, 1, 1),
(13, 33, 'simples', 'Espaguete à Carbonara 🍳', 'Molho carbonara com bacon, ovos e queijo pecorino', 'espaguete-carbonara', 42.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Espaguete, bacon, ovos, queijo pecorino, pimenta preta', 20, 1, 1, 2),
(13, 33, 'simples', 'Fettuccine Alfredo 🧀', 'Fettuccine com molho cremoso de parmesão', 'fettuccine-alfredo', 41.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Fettuccine, creme de leite, parmesão, manteiga', 18, 1, 1, 3),
(13, 33, 'simples', 'Fettuccine ao Pesto 🌿', 'Fettuccine com molho pesto de manjericão e pinoli', 'fettuccine-pesto', 40.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Fettuccine, molho pesto, manjericão, pinoli, parmesão', 18, 1, 0, 4),
(13, 33, 'simples', 'Lasanha à Bolonhesa 🍝', 'Camadas de massa, molho bolonhesa e queijo', 'lasanha-bolonhesa', 44.90, 39.90, 'https://images.unsplash.com/photo-1579684947550-22e945225d9a?w=300', 'Massa de lasanha, molho bolonhesa, mussarela, parmesão', 25, 1, 1, 5),
(13, 33, 'simples', 'Lasanha de Frango com Catupiry 🐔', 'Lasanha de frango desfiado com catupiry', 'lasanha-frango-catupiry', 43.90, NULL, 'https://images.unsplash.com/photo-1579684947550-22e945225d9a?w=300', 'Massa, frango, catupiry, mussarela', 25, 1, 1, 6),
(13, 33, 'simples', 'Lasanha de Berinjela (Vegana) 🍆', 'Lasanha vegana de berinjela e molho de tomate', 'lasanha-berinjela', 39.90, NULL, 'https://images.unsplash.com/photo-1579684947550-22e945225d9a?w=300', 'Berinjela, molho de tomate, manjericão', 25, 1, 0, 7),
(13, 33, 'simples', 'Ravioli de Ricota com Espinafre 🥟', 'Massa recheada com ricota e espinafre ao molho de manteiga e sálvia', 'ravioli-ricota-espinafre', 46.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Ravioli, ricota, espinafre, manteiga, sálvia, parmesão', 22, 1, 1, 8),
(13, 33, 'simples', 'Ravioli de Carne 🥟', 'Ravioli de carne ao molho sugo', 'ravioli-carne', 45.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Ravioli de carne, molho sugo, parmesão', 22, 1, 0, 9),
(13, 33, 'simples', 'Ravioli de Queijo 🥟', 'Ravioli de queijo ao molho quatro queijos', 'ravioli-queijo', 44.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Ravioli de queijo, molho quatro queijos', 22, 1, 0, 10),
(13, 33, 'simples', 'Nhoque ao Sugo 🥔', 'Nhoque de batata com molho sugo', 'nhoque-sugo', 36.90, 32.90, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Nhoque de batata, molho sugo, parmesão', 18, 1, 1, 11),
(13, 33, 'simples', 'Nhoque ao Pesto 🥔', 'Nhoque de batata com molho pesto', 'nhoque-pesto', 38.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Nhoque, molho pesto, parmesão', 18, 1, 0, 12),
(13, 33, 'simples', 'Nhoque ao Molho de Gorgonzola 🧀', 'Nhoque de batata com molho cremoso de gorgonzola', 'nhoque-gorgonzola', 41.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Nhoque, molho de gorgonzola, nozes', 18, 1, 1, 13),
(13, 33, 'simples', 'Tortéi de Frango com Catupiry 🥟', 'Tortéi recheado com frango e catupiry', 'tortei-frango-catupiry', 43.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Tortéi, frango, catupiry, molho de tomate', 22, 1, 0, 14),
(13, 33, 'simples', 'Tortéi de Carne com Molho Bolonhesa 🥟', 'Tortéi de carne com molho bolonhesa', 'tortei-carne-bolonhesa', 44.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Tortéi de carne, molho bolonhesa', 22, 1, 0, 15),

-- RISOTOS (subcategoria 34) - 8 produtos
(13, 34, 'simples', 'Risoto de Funghi Porcini 🍄', 'Risoto cremoso com cogumelos funghi porcini', 'risoto-funghi', 48.90, 43.90, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Arroz arbóreo, funghi porcini, parmesão, vinho branco', 25, 1, 1, 16),
(13, 34, 'simples', 'Risoto de Camarão 🦐', 'Risoto cremoso com camarões e toque de limão siciliano', 'risoto-camarao', 52.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Arroz arbóreo, camarões, limão siciliano, parmesão, salsinha', 28, 1, 1, 17),
(13, 34, 'simples', 'Risoto de Frango com Curry 🐔', 'Risoto cremoso de frango com curry', 'risoto-frango-curry', 42.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Arroz arbóreo, frango, curry, parmesão', 22, 1, 0, 18),
(13, 34, 'simples', 'Risoto de Cogumelos Paris 🍄', 'Risoto com cogumelos Paris frescos', 'risoto-cogumelos-paris', 44.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Arroz arbóreo, cogumelos Paris, parmesão, vinho branco', 22, 1, 1, 19),
(13, 34, 'simples', 'Risoto de Limão Siciliano 🍋', 'Risoto leve com limão siciliano e raspas', 'risoto-limao', 39.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Arroz arbóreo, limão siciliano, parmesão, manjericão', 20, 1, 0, 20),
(13, 34, 'simples', 'Risoto de Gorgonzola com Nozes 🧀', 'Risoto cremoso de gorgonzola com nozes', 'risoto-gorgonzola-nozes', 46.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Arroz arbóreo, gorgonzola, nozes, parmesão', 22, 1, 1, 21),
(13, 34, 'simples', 'Risoto de Pesto de Manjericão 🌿', 'Risoto ao molho pesto', 'risoto-pesto', 41.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Arroz arbóreo, molho pesto, parmesão', 20, 1, 0, 22),
(13, 34, 'simples', 'Risoto de Abóbora com Gengibre 🎃', 'Risoto de abóbora com toque de gengibre', 'risoto-abobora-gengibre', 40.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Arroz arbóreo, abóbora, gengibre, parmesão', 22, 1, 0, 23),

-- POLENTAS (subcategoria 35) - 4 produtos
(13, 35, 'simples', 'Polenta Frita (8 unidades) 🟨', 'Polenta frita crocante', 'polenta-frita-8', 18.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Polenta frita, sal grosso', 10, 1, 0, 24),
(13, 35, 'simples', 'Polenta Cremosa com Ragù de Carne 🥩', 'Polenta cremosa com molho de carne', 'polenta-cremosa-ragu', 36.90, 32.90, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Polenta cremosa, ragù de carne, parmesão', 15, 1, 1, 25),
(13, 35, 'simples', 'Polenta Cremosa com Cogumelos 🍄', 'Polenta cremosa com cogumelos salteados', 'polenta-cremosa-cogumelos', 34.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Polenta cremosa, cogumelos, parmesão', 15, 1, 1, 26),
(13, 35, 'simples', 'Polenta com Gorgonzola e Nozes 🧀', 'Polenta cremosa com gorgonzola e nozes', 'polenta-gorgonzola-nozes', 38.90, NULL, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=300', 'Polenta cremosa, gorgonzola, nozes', 15, 1, 0, 27),

-- CARNES (subcategoria 22) - 5 produtos
(13, 22, 'simples', 'Filé à Parmegiana 🥩', 'Filé empanado, molho de tomate, mussarela e presunto', 'file-parmegiana-nonna', 52.90, 46.90, 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e8?w=300', 'Filé mignon, molho de tomate, mussarela, presunto, arroz, batata', 25, 1, 1, 28),
(13, 22, 'simples', 'Bife à Milanesa 🥩', 'Bife empanado à milanesa', 'bife-milanesa-nonna', 42.90, NULL, 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e8?w=300', 'Bife empanado, arroz, batata frita, salada', 20, 1, 0, 29),
(13, 22, 'simples', 'Escalope de Frango ao Molho de Limão 🍋', 'Filé de frango ao molho de limão', 'escalope-frango-limao', 44.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Frango, molho de limão, arroz, legumes', 20, 1, 1, 30),
(13, 22, 'simples', 'Ossobuco à Milanesa 🥩', 'Ossobuco cozido com legumes e vinho branco', 'ossobuco', 58.90, 52.90, 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=300', 'Ossobuco, legumes, vinho branco, polenta', 35, 1, 1, 31),
(13, 22, 'simples', 'Frango à Cacciatora 🐔', 'Frango cozido com tomates, azeitonas e ervas', 'frango-cacciatora', 48.90, NULL, 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=300', 'Frango, tomate, azeitona, ervas, arroz', 30, 1, 0, 32),

-- ANTIPASTI (subcategoria 21) - 3 produtos
(13, 21, 'simples', 'Bruschetta Tradicional (4 unidades) 🥖', 'Pão italiano com tomate, manjericão e azeite', 'bruschetta-4', 22.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Pão italiano, tomate, manjericão, alho, azeite', 10, 1, 1, 33),
(13, 21, 'simples', 'Antipasto Misto 🧀', 'Mix de frios e queijos italianos', 'antipasto-misto', 42.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Salame, presunto de parma, provolone, azeitonas', 10, 1, 1, 34),
(13, 21, 'simples', 'Carpaccio de Carne 🥩', 'Finas fatias de carne crua com rúcula e parmesão', 'carpaccio', 39.90, NULL, 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=300', 'Carne, rúcula, parmesão, mostarda', 12, 1, 0, 35);


-- =====================================================
-- LOJA 14: Café Central (35 produtos - cafeteria)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- CAFÉS QUENTES (subcategoria 23) - 10 produtos
(14, 23, 'simples', 'Café Expresso (50ml) ☕', 'Café puro e encorpado', 'cafe-expresso', 5.90, 4.90, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café expresso', 2, 1, 1, 1),
(14, 23, 'simples', 'Café Expresso Duplo (100ml) ☕☕', 'Café expresso em dobro', 'cafe-expresso-duplo', 8.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café expresso duplo', 3, 1, 0, 2),
(14, 23, 'simples', 'Cappuccino Tradicional 🥤', 'Café, leite vaporizado, espuma de leite e chocolate', 'cappuccino-tradicional', 9.90, 8.90, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café expresso, leite vaporizado, espuma, chocolate', 5, 1, 1, 3),
(14, 23, 'simples', 'Cappuccino Italiano 🇮🇹', 'Cappuccino cremoso com chocolate italiano', 'cappuccino-italiano', 11.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite, chocolate italiano, crema', 6, 1, 1, 4),
(14, 23, 'simples', 'Latte Macchiato 🥛', 'Leite vaporizado com café', 'latte-macchiato', 10.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Leite vaporizado, café expresso', 5, 1, 0, 5),
(14, 23, 'simples', 'Mocha Chocolate 🍫', 'Café com leite e chocolate', 'mocha-chocolate', 11.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite, calda de chocolate, chantilly', 6, 1, 1, 6),
(14, 23, 'simples', 'Caramelo Macchiato 🍯', 'Café com leite e calda de caramelo', 'caramelo-macchiato', 12.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café, leite, calda de caramelo, chantilly', 6, 1, 1, 7),
(14, 23, 'simples', 'Pingado ☕', 'Café com um pouco de leite', 'pingado', 6.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café, leite', 3, 1, 0, 8),
(14, 23, 'simples', 'Café com Leite ☕🥛', 'Café e leite na medida certa', 'cafe-com-leite', 7.90, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300', 'Café, leite', 4, 1, 0, 9),
(14, 23, 'simples', 'Chocolate Quente 🍫', 'Chocolate quente cremoso', 'chocolate-quente', 10.90, NULL, 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=300', 'Leite, chocolate, chantilly', 5, 1, 1, 10),

-- CAFÉS GELADOS (subcategoria 24) - 6 produtos
(14, 24, 'simples', 'Café Gelado com Gelo 🧊', 'Café expresso com gelo', 'cafe-gelado', 7.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café expresso, gelo', 3, 1, 0, 11),
(14, 24, 'simples', 'Frappuccino de Chocolate 🍫', 'Café batido com chocolate e gelo', 'frappuccino-chocolate', 15.90, 13.90, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café, chocolate, leite, gelo, chantilly', 7, 1, 1, 12),
(14, 24, 'simples', 'Frappuccino de Caramelo 🍯', 'Café batido com caramelo e gelo', 'frappuccino-caramelo', 15.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café, caramelo, leite, gelo, chantilly', 7, 1, 1, 13),
(14, 24, 'simples', 'Frappuccino de Morango 🍓', 'Café batido com morango e gelo', 'frappuccino-morango', 16.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café, morango, leite, gelo, chantilly', 7, 1, 0, 14),
(14, 24, 'simples', 'Mocha Gelado 🍫', 'Mocha com gelo', 'mocha-gelado', 14.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café, chocolate, leite, gelo', 5, 1, 0, 15),
(14, 24, 'simples', 'Affogato ☕🍨', 'Café expresso com sorvete de creme', 'affogato', 14.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café expresso, sorvete de creme', 5, 1, 1, 16),

-- SALGADOS (subcategoria 25) - 8 produtos
(14, 25, 'simples', 'Pão de Queijo (6 unidades) 🧀', 'Pão de queijo mineiro tradicional', 'pao-de-queijo-6', 9.90, 8.90, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Pão de queijo', 8, 1, 1, 17),
(14, 25, 'simples', 'Pão de Queijo (12 unidades) 🧀', 'Porção família de pão de queijo', 'pao-de-queijo-12', 17.90, 15.90, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Pão de queijo', 12, 1, 1, 18),
(14, 25, 'simples', 'Coxinha de Frango (unidade) 🥟', 'Coxinha de frango com catupiry', 'coxinha-unidade', 6.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Frango, catupiry, massa', 5, 1, 1, 19),
(14, 25, 'simples', 'Coxinha de Frango (6 unidades) 🥟', 'Porção de coxinhas', 'coxinha-6', 34.90, 29.90, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', '6 coxinhas de frango com catupiry', 15, 1, 1, 20),
(14, 25, 'simples', 'Empada de Frango (unidade) 🥧', 'Empada de frango', 'empada-frango', 7.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Frango, massa podre', 5, 1, 0, 21),
(14, 25, 'simples', 'Empada de Palmito (unidade) 🌴', 'Empada de palmito', 'empada-palmito', 8.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Palmito, massa podre', 5, 1, 0, 22),
(14, 25, 'simples', 'Risoles de Carne (unidade) 🥟', 'Risoles de carne', 'risoles-carne', 6.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Carne moída, massa', 5, 1, 0, 23),
(14, 25, 'simples', 'Misto Quente (Sanduíche) 🥪', 'Pão de forma, presunto e queijo', 'misto-quente', 12.90, NULL, 'https://images.unsplash.com/photo-1528736235302-52922df5c122?w=300', 'Pão, presunto, queijo', 5, 1, 0, 24),

-- DOCES (subcategoria 26) - 7 produtos
(14, 26, 'simples', 'Brownie de Chocolate (unidade) 🍫', 'Brownie de chocolate com nozes', 'brownie-unidade', 8.90, NULL, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Chocolate, nozes, manteiga', 3, 1, 1, 25),
(14, 26, 'simples', 'Brownie de Chocolate com Sorvete 🍨', 'Brownie quente com sorvete de creme', 'brownie-sorvete', 16.90, 14.90, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Brownie, sorvete, calda de chocolate', 5, 1, 1, 26),
(14, 26, 'simples', 'Cookie de Chocolate (unidade) 🍪', 'Cookie recheado de chocolate', 'cookie-chocolate', 6.90, NULL, 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=300', 'Massa, chocolate', 3, 1, 0, 27),
(14, 26, 'simples', 'Cookie de Chocolate com Gotas 🍪', 'Cookie com gotas de chocolate', 'cookie-gotas', 7.90, NULL, 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=300', 'Massa, gotas de chocolate', 3, 1, 0, 28),
(14, 26, 'simples', 'Donut de Chocolate 🍩', 'Donut com cobertura de chocolate', 'donut-chocolate', 8.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, chocolate granulado', 3, 1, 1, 29),
(14, 26, 'simples', 'Donut de Morango 🍓', 'Donut com cobertura de morango', 'donut-morango', 8.90, NULL, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300', 'Massa, cobertura de morango', 3, 1, 0, 30),
(14, 26, 'simples', 'Pudim de Leite (fatia) 🍮', 'Pudim de leite condensado', 'pudim-fatia', 9.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Leite condensado, leite, ovos', 3, 1, 1, 31),

-- BEBIDAS NÃO CAFEINADAS (subcategoria 14) - 4 produtos
(14, 14, 'simples', 'Suco de Laranja Natural (300ml) 🍊', 'Suco de laranja fresco', 'suco-laranja-300', 8.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Laranja in natura', 5, 1, 1, 32),
(14, 14, 'simples', 'Suco de Limão (300ml) 🍋', 'Suco de limão fresco', 'suco-limao-300', 7.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Limão, açúcar', 5, 1, 0, 33),
(14, 14, 'simples', 'Chá Gelado de Pêssego (350ml) 🍑', 'Chá de pêssego gelado', 'cha-gelado-pessego', 7.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Chá, pêssego, gelo', 3, 1, 0, 34),
(14, 14, 'simples', 'Água Mineral (500ml) 💧', 'Água sem gás', 'agua-500', 4.90, NULL, 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=300', 'Água', 1, 1, 0, 35);


-- =====================================================
-- LOJA 15: Coffee Lab (35 produtos - cafés especiais)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- CAFÉS ESPECIAIS (subcategoria 23) - 12 produtos
(15, 23, 'simples', 'Café Expresso Single Origin 🇧🇷', 'Café 100% arábica de origem única', 'expresso-single-origin', 7.90, 6.90, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café arábica especial', 3, 1, 1, 1),
(15, 23, 'simples', 'Café Expresso Blend da Casa ☕', 'Blend exclusivo do Coffee Lab', 'expresso-blend-casa', 8.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Blend especial de cafés', 3, 1, 1, 2),
(15, 23, 'simples', 'Aeropress 🧪', 'Café preparado no método Aeropress', 'aeropress', 12.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café especial preparado no Aeropress', 5, 1, 1, 3),
(15, 23, 'simples', 'V60 (Hario) 🫗', 'Café coado no método V60', 'v60', 11.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café coado no V60', 5, 1, 1, 4),
(15, 23, 'simples', 'Chemex ☕', 'Café preparado na Chemex', 'chemex', 13.90, 11.90, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café especial preparado na Chemex', 6, 1, 1, 5),
(15, 23, 'simples', 'French Press 🇫🇷', 'Café prensado francês', 'french-press', 10.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café especial prensado', 5, 1, 0, 6),
(15, 23, 'simples', 'Cold Brew (200ml) 🧊', 'Café de extração a frio por 12 horas', 'cold-brew-200', 14.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café extraído a frio', 2, 1, 1, 7),
(15, 23, 'simples', 'Cold Brew Tônica (300ml) 🍋', 'Cold Brew com água tônica e limão', 'cold-brew-tonica', 16.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Cold brew, água tônica, limão siciliano', 3, 1, 1, 8),
(15, 23, 'simples', 'Nitro Cold Brew (300ml) 🧊', 'Cold Brew nitrogenado', 'nitro-cold-brew', 18.90, 16.90, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Cold brew nitrogenado', 2, 1, 1, 9),
(15, 23, 'simples', 'Café Geisha (edição limitada) 🏆', 'Café Geisha premium', 'cafe-geisha', 28.90, 24.90, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café Geisha especial', 5, 1, 1, 10),
(15, 23, 'simples', 'Café Bourbon Amarelo 🟡', 'Café Bourbon Amarelo especial', 'bourbon-amarelo', 16.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café Bourbon Amarelo', 4, 1, 0, 11),
(15, 23, 'simples', 'Café Catuaí Vermelho 🔴', 'Café Catuaí Vermelho', 'catuai-vermelho', 15.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café Catuaí Vermelho', 4, 1, 0, 12),

-- CAFÉS COM LEITE ESPECIAIS (subcategoria 23) - 6 produtos
(15, 23, 'simples', 'Latte Art Perfeito 🎨', 'Café com leite e arte na superfície', 'latte-art', 12.90, 10.90, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café expresso, leite vaporizado', 6, 1, 1, 13),
(15, 23, 'simples', 'Flat White 🇦🇺', 'Café com microespuma de leite', 'flat-white', 13.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café expresso, leite vaporizado', 5, 1, 1, 14),
(15, 23, 'simples', 'Piccolo Latte 🥛', 'Latte pequeno e encorpado', 'piccolo-latte', 10.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café expresso, leite vaporizado', 4, 1, 0, 15),
(15, 23, 'simples', 'Macchiato 🇮🇹', 'Café com uma mancha de leite', 'macchiato', 9.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café expresso, espuma de leite', 3, 1, 0, 16),
(15, 23, 'simples', 'Cortado 🇪🇸', 'Café cortado com leite', 'cortado', 10.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café expresso, leite', 4, 1, 0, 17),
(15, 23, 'simples', 'Gibraltar 🥃', 'Café servido no copo Gibraltar', 'gibraltar', 11.90, NULL, 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300', 'Café expresso, leite vaporizado', 4, 1, 1, 18),

-- SALGADOS GOURMET (subcategoria 25) - 6 produtos
(15, 25, 'simples', 'Pão de Queijo Gourmet com Requeijão 🧀', 'Pão de queijo especial com requeijão cremoso', 'pao-queijo-gourmet', 12.90, NULL, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', 'Pão de queijo, requeijão', 8, 1, 1, 19),
(15, 25, 'simples', 'Croissant de Presunto e Queijo 🥐', 'Croissant folhado recheado', 'croissant-presunto-queijo', 14.90, NULL, 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=300', 'Croissant, presunto, queijo', 5, 1, 1, 20),
(15, 25, 'simples', 'Croissant de Frango com Catupiry 🥐', 'Croissant de frango com catupiry', 'croissant-frango-catupiry', 15.90, NULL, 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=300', 'Croissant, frango, catupiry', 5, 1, 1, 21),
(15, 25, 'simples', 'Empadão de Palmito (fatia) 🥧', 'Empadão de palmito', 'empadao-palmito', 16.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Palmito, massa podre', 8, 1, 0, 22),
(15, 25, 'simples', 'Torta de Alho Poró 🥧', 'Torta salgada de alho poró', 'torta-alho-poro', 17.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Alho poró, queijo, massa', 8, 1, 0, 23),
(15, 25, 'simples', 'Quiche Lorraine 🇫🇷', 'Quiche de bacon e queijo', 'quiche-lorraine', 18.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Massa, bacon, queijo, ovos', 10, 1, 1, 24),

-- DOCES FINOS (subcategoria 26) - 6 produtos
(15, 26, 'simples', 'Brownie com Nozes e Caramelo 🍫', 'Brownie premium com nozes e calda de caramelo', 'brownie-nozes-caramelo', 14.90, 12.90, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Chocolate, nozes, caramelo', 5, 1, 1, 25),
(15, 26, 'simples', 'Cheesecake de Frutas Vermelhas 🍓', 'Cheesecake com calda de frutas vermelhas', 'cheesecake-frutas', 16.90, NULL, 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=300', 'Queijo cream, frutas vermelhas', 5, 1, 1, 26),
(15, 26, 'simples', 'Torta de Limão Siciliano 🍋', 'Torta de limão com merengue', 'torta-limao-siciliano', 15.90, NULL, 'https://images.unsplash.com/photo-1519915028121-7d3463d20b13?w=300', 'Limão siciliano, massa', 5, 1, 1, 27),
(15, 26, 'simples', 'Macaron Francês (4 unidades) 🇫🇷', 'Macarons variados', 'macaron-4', 18.90, NULL, 'https://images.unsplash.com/photo-1569864358642-9d1684040f43?w=300', 'Amêndoas, recheio variado', 5, 1, 0, 28),
(15, 26, 'simples', 'Financier de Amêndoas 🍰', 'Bolo francês de amêndoas', 'financier-amendoas', 12.90, NULL, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Amêndoas, manteiga', 4, 1, 0, 29),
(15, 26, 'simples', 'Creme Brûlée 🇫🇷', 'Creme brûlée com crosta de açúcar queimado', 'creme-brulee', 17.90, NULL, 'https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=300', 'Creme, baunilha, açúcar', 8, 1, 1, 30),

-- CAFÉS EM GRÃOS (subcategoria 23) - 5 produtos (para levar)
(15, 23, 'simples', 'Café em Grãos - Blend da Casa (250g) ☕', 'Blend exclusivo do Coffee Lab', 'graos-blend-casa-250g', 28.90, 24.90, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café em grãos', 1, 1, 1, 31),
(15, 23, 'simples', 'Café em Grãos - Single Origin (250g) 🇧🇷', 'Café de origem única', 'graos-single-origin-250g', 32.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café em grãos', 1, 1, 1, 32),
(15, 23, 'simples', 'Café Moído - Blend da Casa (250g) ☕', 'Blend exclusivo moído', 'moido-blend-casa-250g', 26.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Café moído', 1, 1, 0, 33),
(15, 23, 'simples', 'Cápsulas de Café (10 unidades) 💊', 'Cápsulas compatíveis Nespresso', 'capsulas-10', 24.90, NULL, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', 'Cápsulas de café', 1, 1, 0, 34),
(15, 23, 'simples', 'Kit Degustação 3 Cafés (150g cada) 🎁', '3 cafés especiais para degustar', 'kit-degustacao-3', 69.90, 59.90, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300', '3 cafés especiais', 1, 1, 1, 35);



-- =====================================================
-- LOJA 16: Sabor da Terra (35 produtos - comida saudável)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- SALADAS (subcategoria 19) - 8 produtos
(16, 19, 'simples', 'Salada Verde 🌿', 'Mix de folhas, tomate cereja, pepino', 'salada-verde', 22.90, 19.90, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300', 'Alface, rúcula, agrião, tomate cereja, pepino', 8, 1, 1, 1),
(16, 19, 'simples', 'Salada Caesar 🥗', 'Alface, frango grelhado, croutons, parmesão', 'salada-caesar', 28.90, 24.90, 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=300', 'Alface, frango, croutons, parmesão, molho caesar', 10, 1, 1, 2),
(16, 19, 'simples', 'Salada de Quinoa 🌾', 'Quinoa, mix de folhas, legumes, castanhas', 'salada-quinoa', 26.90, NULL, 'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=300', 'Quinoa, rúcula, cenoura, beterraba, castanhas', 8, 1, 1, 3),
(16, 19, 'simples', 'Salada de Grãos 🫘', 'Grão de bico, lentilha, quinoa, legumes', 'salada-graos', 24.90, NULL, 'https://images.unsplash.com/photo-1515543904379-3d757f7a6e4e?w=300', 'Grão de bico, lentilha, quinoa, cenoura, salsinha', 8, 1, 0, 4),
(16, 19, 'simples', 'Salada Tropical 🥭', 'Mix de folhas, manga, abacaxi, castanhas', 'salada-tropical', 27.90, NULL, 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=300', 'Alface, rúcula, manga, abacaxi, castanha de caju', 8, 1, 1, 5),
(16, 19, 'simples', 'Salada de Batata Doce 🍠', 'Batata doce, rúcula, tomate seco, queijo minas', 'salada-batata-doce', 25.90, NULL, 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300', 'Batata doce, rúcula, tomate seco, queijo minas', 10, 1, 0, 6),
(16, 19, 'simples', 'Salada de Abacate 🥑', 'Abacate, mix de folhas, tomate, limão', 'salada-abacate', 29.90, 25.90, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300', 'Abacate, alface, rúcula, tomate, limão siciliano', 8, 1, 1, 7),
(16, 19, 'simples', 'Salada de Beterraba Assada 🔴', 'Beterraba assada, rúcula, queijo de cabra, nozes', 'salada-beterraba', 28.90, NULL, 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=300', 'Beterraba, rúcula, queijo de cabra, nozes, mel', 12, 1, 0, 8),

-- PRATOS QUENTES (subcategoria 20) - 10 produtos
(16, 20, 'simples', 'Filé de Frango Grelhado 🐔', 'Filé de frango grelhado, arroz integral, legumes', 'file-frango', 32.90, 29.90, 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=300', 'Frango grelhado, arroz integral, brócolis, cenoura', 15, 1, 1, 9),
(16, 20, 'simples', 'Filé de Peixe com Legumes 🐟', 'Filé de peixe grelhado, purê de batata doce, legumes', 'file-peixe', 36.90, 32.90, 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=300', 'Peixe, purê de batata doce, abobrinha, cenoura', 18, 1, 1, 10),
(16, 20, 'simples', 'Carne de Panela 🍖', 'Carne de panela, mandioca, arroz integral, vinagrete', 'carne-panela', 34.90, NULL, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=300', 'Carne, mandioca, arroz integral, tomate, cebola', 20, 1, 1, 11),
(16, 20, 'simples', 'Strogonoff de Frango 🍄', 'Strogonoff de frango, arroz integral, batata palha', 'strogonoff-frango', 31.90, 28.90, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Frango, creme de leite, cogumelos, arroz, batata palha', 15, 1, 1, 12),
(16, 20, 'simples', 'Strogonoff de Cogumelos 🍄', 'Strogonoff de cogumelos, arroz integral, batata palha', 'strogonoff-cogumelos', 33.90, NULL, 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=300', 'Cogumelos, creme de leite vegetal, arroz, batata palha', 15, 1, 0, 13),
(16, 20, 'simples', 'Lasanha de Abobrinha 🍆', 'Lasanha de abobrinha com molho bolonhesa', 'lasanha-abobrinha', 35.90, NULL, 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=300', 'Abobrinha, carne moída, molho de tomate, queijo', 20, 1, 1, 14),
(16, 20, 'simples', 'Omelete de Forno 🍳', 'Omelete de forno com legumes e queijo', 'omelete-forno', 26.90, NULL, 'https://images.unsplash.com/photo-1510693206972-df098062cb71?w=300', 'Ovos, queijo, tomate, cebola, espinafre', 12, 1, 0, 15),
(16, 20, 'simples', 'Quibe de Forno Vegano 🫘', 'Quibe de forno vegano com quinoa e grão de bico', 'quibe-vegano', 29.90, NULL, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=300', 'Quinoa, grão de bico, hortelã, cebola, especiarias', 15, 1, 0, 16),
(16, 20, 'simples', 'Escondidinho de Carne Seca 🥔', 'Escondidinho de carne seca com purê de mandioca', 'escondidinho-carne', 38.90, 34.90, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=300', 'Carne seca, mandioca, queijo coalho', 25, 1, 1, 17),
(16, 20, 'simples', 'Escondidinho de Frango 🥔', 'Escondidinho de frango com purê de batata doce', 'escondidinho-frango', 35.90, NULL, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=300', 'Frango, batata doce, requeijão', 25, 1, 0, 18),

-- SOPAS (subcategoria 21) - 5 produtos
(16, 21, 'simples', 'Sopa de Legumes (500ml) 🥣', 'Sopa de legumes frescos', 'sopa-legumes-500', 18.90, NULL, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Cenoura, abobrinha, batata, chuchu, cebola', 10, 1, 1, 19),
(16, 21, 'simples', 'Sopa de Mandioca com Carne (500ml) 🥣', 'Sopa de mandioca com carne', 'sopa-mandioca-carne', 24.90, NULL, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Mandioca, carne, legumes', 12, 1, 1, 20),
(16, 21, 'simples', 'Caldo Verde (500ml) 🥬', 'Caldo verde com couve e calabresa', 'caldo-verde-500', 22.90, NULL, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Batata, couve, calabresa', 12, 1, 1, 21),
(16, 21, 'simples', 'Sopa de Abóbora com Gengibre (500ml) 🎃', 'Sopa cremosa de abóbora com gengibre', 'sopa-abobora', 21.90, NULL, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Abóbora, gengibre, cebola, creme de leite', 10, 1, 0, 22),
(16, 21, 'simples', 'Sopa de Feijão (500ml) 🫘', 'Sopa de feijão com bacon e legumes', 'sopa-feijao', 23.90, NULL, 'https://images.unsplash.com/photo-1547592166-23ac45744e7d?w=300', 'Feijão, bacon, cenoura, couve', 12, 1, 0, 23),

-- SUCO DETOX (subcategoria 14) - 6 produtos
(16, 14, 'simples', 'Suco Verde (500ml) 🥬', 'Couve, limão, maçã, gengibre', 'suco-verde', 14.90, 12.90, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Couve, limão, maçã, gengibre, água de coco', 5, 1, 1, 24),
(16, 14, 'simples', 'Suco Detox (500ml) 🥒', 'Pepino, couve, limão, hortelã', 'suco-detox', 15.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Pepino, couve, limão, hortelã, gengibre', 5, 1, 1, 25),
(16, 14, 'simples', 'Suco Energético (500ml) 🍊', 'Laranja, cenoura, beterraba, gengibre', 'suco-energetico', 16.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Laranja, cenoura, beterraba, gengibre', 5, 1, 1, 26),
(16, 14, 'simples', 'Suco Antioxidante (500ml) 🫐', 'Açaí, morango, banana, mel', 'suco-antioxidante', 18.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Açaí, morango, banana, mel', 5, 1, 0, 27),
(16, 14, 'simples', 'Suco de Clorofila (500ml) 🌱', 'Clorofila, limão, maçã, gengibre', 'suco-clorofila', 17.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Clorofila, limão, maçã, gengibre', 5, 1, 0, 28),
(16, 14, 'simples', 'Água de Coco (500ml) 🥥', 'Água de coco natural', 'agua-coco-500', 8.90, NULL, 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=300', 'Água de coco', 2, 1, 0, 29),

-- SOBREMAS SAUDÁVEIS (subcategoria 26) - 6 produtos
(16, 26, 'simples', 'Banana Split Saudável 🍌', 'Banana, sorvete de banana, mel, granola', 'banana-split', 19.90, 16.90, 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=300', 'Banana, sorvete de banana, mel, granola, frutas vermelhas', 5, 1, 1, 30),
(16, 26, 'simples', 'Mousse de Maracujá Fit 🍈', 'Mousse de maracujá zero açúcar', 'mousse-maracuja', 14.90, NULL, 'https://images.unsplash.com/photo-1544525977-0a3bca9e560d?w=300', 'Maracujá, leite condensado diet, creme de leite', 5, 1, 1, 31),
(16, 26, 'simples', 'Pudim de Chia 🍮', 'Pudim de chia com leite de coco e frutas', 'pudim-chia', 16.90, NULL, 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=300', 'Chia, leite de coco, mel, frutas vermelhas', 5, 1, 0, 32),
(16, 26, 'simples', 'Bolo de Banana Integral 🍌', 'Bolo de banana integral sem açúcar', 'bolo-banana', 12.90, NULL, 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=300', 'Banana, farinha integral, ovos, canela', 5, 1, 1, 33),
(16, 26, 'simples', 'Cookie Integral de Aveia 🍪', 'Cookie integral de aveia com gotas de chocolate 70%', 'cookie-aveia', 8.90, NULL, 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=300', 'Aveia, chocolate 70%, mel, óleo de coco', 5, 1, 0, 34),
(16, 26, 'simples', 'Taça de Frutas com Iogurte 🍓', 'Frutas frescas com iogurte natural e mel', 'taca-frutas', 18.90, NULL, 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=300', 'Morango, banana, uva, iogurte natural, mel, granola', 5, 1, 1, 35);



-- =====================================================
-- LOJA 17: Hamburgueria do Bairro (35 produtos - hamburgueria artesanal)
-- =====================================================

INSERT INTO produto (
    loja_id, subcategoria_id, tipo, nome, descricao, slug, preco, preco_promocional,
    imagem, ingredientes_texto, tempo_preparo_min, disponivel, destaque, ordem
) VALUES
-- HAMBÚRGUERES ARTESANAIS (subcategoria 12) - 15 produtos
(17, 12, 'hamburguer', 'Classic Burger 🍔', 'Pão brioche, hambúrguer 150g, alface, tomate, cebola, maionese da casa', 'classic-burger', 24.90, 22.90, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300', 'Pão brioche, hambúrguer 150g, alface, tomate, cebola roxa, maionese', 12, 1, 1, 1),
(17, 12, 'hamburguer', 'Cheese Burger 🧀', 'Pão brioche, hambúrguer 150g, queijo cheddar, alface, tomate, cebola', 'cheese-burger', 26.90, 24.90, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300', 'Pão brioche, hambúrguer 150g, cheddar, alface, tomate, cebola', 12, 1, 1, 2),
(17, 12, 'hamburguer', 'Bacon Burger 🥓', 'Pão brioche, hambúrguer 150g, queijo, bacon crocante, barbecue', 'bacon-burger', 28.90, 26.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, hambúrguer 150g, queijo prato, bacon, molho barbecue', 15, 1, 1, 3),
(17, 12, 'hamburguer', 'Egg Burger 🥚', 'Pão brioche, hambúrguer 150g, queijo, ovo, alface, tomate', 'egg-burger', 27.90, NULL, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300', 'Pão brioche, hambúrguer 150g, queijo, ovo frito, alface, tomate', 15, 1, 0, 4),
(17, 12, 'hamburguer', 'Duplo Burger 🍔🍔', 'Pão brioche, 2 hambúrgueres 150g, 2 queijos, bacon, molho especial', 'duplo-burger', 36.90, 32.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, 2 hambúrgueres 150g, cheddar, prato, bacon, molho especial', 18, 1, 1, 5),
(17, 12, 'hamburguer', 'Chicken Burger 🐔', 'Pão australiano, filé de frango empanado, queijo, alface, tomate, maionese', 'chicken-burger', 25.90, NULL, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300', 'Pão australiano, frango empanado, queijo, alface, tomate, maionese', 15, 1, 0, 6),
(17, 12, 'hamburguer', 'Veggie Burger 🌱', 'Pão integral, hambúrguer de grão de bico, alface, tomate, rúcula, molho de iogurte', 'veggie-burger', 28.90, NULL, 'https://images.unsplash.com/photo-1525059696034-4967a8e1dca2?w=300', 'Pão integral, hambúrguer de grão de bico, alface, tomate, rúcula, molho de iogurte', 12, 1, 1, 7),
(17, 12, 'hamburguer', 'Burger de Costela 🥩', 'Pão brioche, hambúrguer de costela 150g, queijo, cebola caramelizada, barbecue', 'costela-burger', 32.90, 29.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, hambúrguer de costela 150g, queijo, cebola caramelizada, molho barbecue', 15, 1, 1, 8),
(17, 12, 'hamburguer', 'Burger de Picanha 🥩', 'Pão australiano, hambúrguer de picanha 150g, queijo, alface, tomate, maionese temperada', 'picanha-burger', 34.90, 31.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão australiano, hambúrguer de picanha 150g, queijo, alface, tomate, maionese temperada', 15, 1, 1, 9),
(17, 12, 'hamburguer', 'Burger de Cordeiro 🐑', 'Pão brioche, hambúrguer de cordeiro 150g, queijo de cabra, rúcula, molho de hortelã', 'cordeiro-burger', 38.90, NULL, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, hambúrguer de cordeiro 150g, queijo de cabra, rúcula, molho de hortelã', 16, 1, 0, 10),
(17, 12, 'hamburguer', 'Burger de Salmão 🐟', 'Pão australiano, hambúrguer de salmão 150g, cream cheese, alface, tomate, endro', 'salmao-burger', 42.90, 38.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão australiano, hambúrguer de salmão 150g, cream cheese, alface, tomate, endro', 16, 1, 1, 11),
(17, 12, 'hamburguer', 'Burger Mexicano 🌶️', 'Pão brioche, hambúrguer 150g, queijo, jalapeño, pimenta, guacamole', 'mexicano-burger', 31.90, NULL, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, hambúrguer 150g, queijo, jalapeño, pimenta, guacamole', 15, 1, 0, 12),
(17, 12, 'hamburguer', 'Burger Italiano 🇮🇹', 'Pão italiano, hambúrguer 150g, mussarela de búfala, tomate seco, manjericão', 'italiano-burger', 35.90, NULL, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão italiano, hambúrguer 150g, mussarela de búfala, tomate seco, manjericão', 15, 1, 0, 13),
(17, 12, 'hamburguer', 'Burger de Queijo Coalho 🧀', 'Pão brioche, hambúrguer 150g, queijo coalho grelhado, cebola roxa, melaço', 'coalho-burger', 33.90, NULL, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, hambúrguer 150g, queijo coalho, cebola roxa, melaço de cana', 15, 1, 0, 14),
(17, 12, 'hamburguer', 'Monster Burger 👹', 'Pão brioche, 3 hambúrgueres 150g, 3 queijos, bacon, onion rings, molho especial', 'monster-burger', 49.90, 44.90, 'https://images.unsplash.com/photo-1553979459-d2229ba743c5?w=300', 'Pão brioche, 3 hambúrgueres 150g, cheddar, prato, bacon, onion rings, molho especial', 25, 1, 1, 15),

-- ACOMPANHAMENTOS (subcategoria 13) - 8 produtos
(17, 13, 'simples', 'Batata Frita (300g) 🍟', 'Batata frita crocante', 'batata-frita-300', 14.90, 12.90, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=300', 'Batata, sal', 10, 1, 1, 16),
(17, 13, 'simples', 'Batata Frita com Cheddar e Bacon 🧀🥓', 'Batata frita com cheddar e bacon', 'batata-cheddar-bacon', 21.90, 18.90, 'https://images.unsplash.com/photo-1585109649138-45c85e3e0468?w=300', 'Batata, cheddar, bacon', 12, 1, 1, 17),
(17, 13, 'simples', 'Onion Rings (8 unidades) 🧅', 'Anéis de cebola empanados', 'onion-rings-8', 16.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Cebola, farinha, ovos', 12, 1, 0, 18),
(17, 13, 'simples', 'Polenta Frita (200g) 🌽', 'Polenta frita crocante', 'polenta-frita-200', 15.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Polenta, sal', 12, 1, 0, 19),
(17, 13, 'simples', 'Mandioca Frita (250g) 🥔', 'Mandioca frita', 'mandioca-frita-250', 16.90, NULL, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=300', 'Mandioca, sal', 12, 1, 0, 20),
(17, 13, 'simples', 'Salada Caesar (pequena) 🥗', 'Salada Caesar com frango', 'salada-caesar-p', 19.90, NULL, 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=300', 'Alface, frango, croutons, parmesão, molho caesar', 8, 1, 0, 21),
(17, 13, 'simples', 'Nuggets de Frango (8 unidades) 🍗', 'Nuggets de frango', 'nuggets-8', 18.90, NULL, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=300', 'Frango empanado', 10, 1, 0, 22),
(17, 13, 'simples', 'Combo de Fritas + Nuggets 🍟🍗', 'Batata frita e nuggets', 'combo-fritas-nuggets', 29.90, 25.90, 'https://images.unsplash.com/photo-1585109649138-45c85e3e0468?w=300', 'Batata frita, nuggets de frango', 15, 1, 1, 23),

-- BEBIDAS (subcategoria 14) - 6 produtos
(17, 14, 'simples', 'Refrigerante Lata (350ml) 🥤', 'Coca-Cola, Guaraná, Sprite', 'refrigerante-lata', 6.90, NULL, 'https://images.unsplash.com/photo-1581006852262-4305c2fad871?w=300', 'Refrigerante', 2, 1, 1, 24),
(17, 14, 'simples', 'Refrigerante 1L 🥤', 'Coca-Cola, Guaraná, Sprite', 'refrigerante-1l', 10.90, NULL, 'https://images.unsplash.com/photo-1581006852262-4305c2fad871?w=300', 'Refrigerante', 2, 1, 0, 25),
(17, 14, 'simples', 'Suco Natural (400ml) 🧃', 'Laranja, limão, abacaxi, maracujá', 'suco-natural-400', 11.90, NULL, 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=300', 'Fruta natural', 5, 1, 0, 26),
(17, 14, 'simples', 'Água Mineral (500ml) 💧', 'Água sem gás', 'agua-500', 4.90, NULL, 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=300', 'Água', 2, 1, 0, 27),
(17, 14, 'simples', 'Água com Gás (500ml) 💧', 'Água com gás', 'agua-gas-500', 5.90, NULL, 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=300', 'Água com gás', 2, 1, 0, 28),
(17, 14, 'simples', 'Cerveja Long Neck (355ml) 🍺', 'Heineken, Stella, Budweiser', 'cerveja-long', 9.90, 8.90, 'https://images.unsplash.com/photo-1586994496097-9c0fb7a509ac?w=300', 'Cerveja', 2, 1, 1, 29),

-- MOLHES ESPECIAIS (subcategoria 15) - 3 produtos
(17, 15, 'simples', 'Molho Barbecue (50ml) 🍖', 'Molho barbecue artesanal', 'molho-barbecue', 2.90, NULL, 'https://images.unsplash.com/photo-1581262177161-977bcc9b86b7?w=300', 'Molho barbecue', 2, 1, 0, 30),
(17, 15, 'simples', 'Molho de Pimenta (50ml) 🌶️', 'Molho de pimenta artesanal', 'molho-pimenta', 2.90, NULL, 'https://images.unsplash.com/photo-1581262177161-977bcc9b86b7?w=300', 'Pimenta, especiarias', 2, 1, 0, 31),
(17, 15, 'simples', 'Maionese Temperada (50ml) 🥚', 'Maionese da casa com ervas', 'maionese-temperada', 2.90, NULL, 'https://images.unsplash.com/photo-1581262177161-977bcc9b86b7?w=300', 'Maionese, ervas finas', 2, 1, 0, 32),

-- SOBREMESAS (subcategoria 26) - 3 produtos
(17, 26, 'simples', 'Milk Shake de Chocolate (400ml) 🥤', 'Milk shake de chocolate', 'milkshake-chocolate', 18.90, 16.90, 'https://images.unsplash.com/photo-1572490122744-4ab00e582f71?w=300', 'Sorvete de chocolate, leite, calda de chocolate', 5, 1, 1, 33),
(17, 26, 'simples', 'Milk Shake de Morango (400ml) 🍓', 'Milk shake de morango', 'milkshake-morango', 18.90, NULL, 'https://images.unsplash.com/photo-1572490122744-4ab00e582f71?w=300', 'Sorvete de morango, leite, calda de morango', 5, 1, 0, 34),
(17, 26, 'simples', 'Petit Gateau com Sorvete 🍰', 'Petit gateau com sorvete de creme', 'petit-gateau', 22.90, 19.90, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300', 'Bolo de chocolate, sorvete de creme, calda de chocolate', 8, 1, 1, 35);


