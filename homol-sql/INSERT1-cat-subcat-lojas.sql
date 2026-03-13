-- =====================================================
-- PARTE 1: CATEGORIAS (15 categorias com emojis)
-- =====================================================

INSERT INTO categoria (id, nome, slug, icone, descricao, cor, ordem, ativo, destaque) VALUES
(1, 'Pizzarias 🍕', 'pizzarias', '🍕', 'As melhores pizzas da cidade, com massa artesanal e ingredientes selecionados', '#FF6B6B', 1, 1, 1),
(2, 'Hamburguerias 🍔', 'hamburguerias', '🍔', 'Hambúrgueres artesanais com pão australiano e molhos especiais', '#FFA500', 2, 1, 1),
(3, 'Japonesa 🍣', 'japonesa', '🍣', 'Sushi e culinária japonesa com peixes frescos diariamente', '#FF4444', 3, 1, 1),
(4, 'Brasileira 🇧🇷', 'brasileira', '🇧🇷', 'Comida caseira, buffet por quilo e pratos executivos', '#228B22', 4, 1, 1),
(5, 'Cafeteria ☕', 'cafeteria', '☕', 'Café especial, salgados e sobremesas em ambiente aconchegante', '#8B4513', 5, 1, 1),
(6, 'Mexicana 🌮', 'mexicana', '🌮', 'Autêntica culinária mexicana com tacos, burritos e nachos', '#FF4500', 6, 1, 0),
(7, 'Italiana 🍝', 'italiana', '🍝', 'Massas frescas, risotos e polentas, receitas tradicionais italianas', '#B22222', 7, 1, 1),
(8, 'Açaí & Sorvetes 🥤', 'acai-sorvetes', '🥤', 'Açaí cremoso, sorvetes e sobremesas geladas', '#8A2BE2', 8, 1, 1),
(9, 'Chinesa 🥡', 'chinesa', '🥡', 'Comida chinesa tradicional com delivery rápido', '#DC143C', 9, 1, 0),
(10, 'Doces & Bolos 🎂', 'doces-bolos', '🎂', 'Bolos, doces e sobremesas artesanais', '#FF69B4', 10, 1, 1),
(11, 'Árabe 🥙', 'arabe', '🥙', 'Esfihas, quibes e comida árabe tradicional', '#DAA520', 11, 1, 0),
(12, 'Frutos do Mar 🦐', 'frutos-do-mar', '🦐', 'Peixes, camarões e frutos do mar frescos', '#4682B4', 12, 1, 1),
(13, 'Vegetariana 🌱', 'vegetariana', '🌱', 'Opções saudáveis e vegetarianas', '#32CD32', 13, 1, 1),
(14, 'Bebidas 🧃', 'bebidas', '🧃', 'Refrigerantes, sucos, cervejas e drinks', '#2196F3', 14, 1, 0),
(15, 'Padaria 🥖', 'padaria', '🥖', 'Pães artesanais, doces e salgados', '#DEB887', 15, 1, 0);


-- =====================================================
-- PARTE 2: SUBCATEGORIAS (47 subcategorias)
-- =====================================================

INSERT INTO subcategoria (categoria_id, nome, slug, icone, descricao, ordem, ativo) VALUES
-- Pizzarias (categoria 1)
(1, 'Pizzas Salgadas 🧂', 'pizzas-salgadas', '🧂', 'Sabores tradicionais e especiais', 1, 1),
(1, 'Pizzas Doces 🍫', 'pizzas-doces', '🍫', 'Pizzas com chocolate, banana e mais', 2, 1),
(1, 'Meio a Meio 🤝', 'meio-a-meio', '🤝', 'Escolha dois sabores na mesma pizza', 3, 1),
(1, 'Bordas Recheadas 🧀', 'bordas-recheadas', '🧀', 'Catupiry, cheddar, chocolate', 4, 1),
(1, 'Calzones 🥟', 'calzones', '🥟', 'Calzones recheados', 5, 1),

-- Hamburguerias (categoria 2)
(2, 'Hambúrgueres Simples 🍔', 'hamburgueres-simples', '🍔', 'Hambúrgueres tradicionais', 1, 1),
(2, 'Hambúrgueres Artesanais 🧑‍🍳', 'hamburgueres-artesanais', '🧑‍🍳', 'Hambúrgueres gourmet', 2, 1),
(2, 'Combos 🍟', 'combos', '🍟', 'Hambúrguer + batata + bebida', 3, 1),
(2, 'Batatas Fritas 🍟', 'batatas-fritas', '🍟', 'Porções de batata com molhos', 4, 1),
(2, 'Adicionais 🧀', 'adicionais', '🧀', 'Bacon, queijo, ovo, calabresa', 5, 1),

-- Japonesa (categoria 3)
(3, 'Combinados 🍱', 'combinados', '🍱', 'Combinados de sushi para compartilhar', 1, 1),
(3, 'Sushis 🍣', 'sushis', '🍣', 'Sushis variados', 2, 1),
(3, 'Sashimis 🐟', 'sashimis', '🐟', 'Fatias de peixe fresco', 3, 1),
(3, 'Temakis 🍥', 'temakis', '🍥', 'Temakis de diversos sabores', 4, 1),
(3, 'Hot Rolls 🔥', 'hot-rolls', '🔥', 'Hot rolls empanados', 5, 1),
(3, 'Entradas Japonesas 🥟', 'entradas-japonesas', '🥟', 'Guioza, sunomono, missoshiru', 6, 1),

-- Brasileira (categoria 4)
(4, 'Pratos Executivos 🍽️', 'pratos-executivos', '🍽️', 'Pratos do dia', 1, 1),
(4, 'PF (Prato Feito) 🍛', 'prato-feito', '🍛', 'Arroz, feijão, carne e acompanhamentos', 2, 1),
(4, 'Feijoada 🥘', 'feijoada', '🥘', 'Feijoada completa', 3, 1),
(4, 'Petiscos 🥜', 'petiscos', '🥜', 'Porções para compartilhar', 4, 1),
(4, 'Carnes 🥩', 'carnes', '🥩', 'Picanha, filé, frango grelhado', 5, 1),

-- Cafeteria (categoria 5)
(5, 'Cafés Quentes ☕', 'cafes-quentes', '☕', 'Expresso, cappuccino, latte', 1, 1),
(5, 'Cafés Gelados 🧊', 'cafes-gelados', '🧊', 'Café gelado, frappuccino', 2, 1),
(5, 'Salgados 🥐', 'salgados', '🥐', 'Coxinha, empada, pão de queijo', 3, 1),
(5, 'Doces de Cafeteria 🍩', 'doces-cafeteria', '🍩', 'Donuts, brownies, cookies', 4, 1),

-- Mexicana (categoria 6)
(6, 'Tacos 🌮', 'tacos', '🌮', 'Tacos tradicionais', 1, 1),
(6, 'Burritos 🌯', 'burritos', '🌯', 'Burritos recheados', 2, 1),
(6, 'Nachos 🧀', 'nachos', '🧀', 'Nachos com cheddar', 3, 1),
(6, 'Quesadillas 🫓', 'quesadillas', '🫓', 'Quesadillas de frango ou carne', 4, 1),

-- Italiana (categoria 7)
(7, 'Massas 🍝', 'massas', '🍝', 'Macarrão, lasanha, nhoque', 1, 1),
(7, 'Risotos 🍚', 'risotos', '🍚', 'Risotos variados', 2, 1),
(7, 'Polentas 🟨', 'polentas', '🟨', 'Polenta frita e cremosa', 3, 1),

-- Açaí & Sorvetes (categoria 8)
(8, 'Açaí na Tigela 🥣', 'acai-tigela', '🥣', 'Açaí com granola e banana', 1, 1),
(8, 'Sorvetes 🍦', 'sorvetes', '🍦', 'Sorvetes de massa', 2, 1),
(8, 'Milkshakes 🥛', 'milkshakes', '🥛', 'Milkshakes cremosos', 3, 1),
(8, 'Picolés 🍡', 'picoles', '🍡', 'Picolés diversos', 4, 1),

-- Chinesa (categoria 9)
(9, 'Yakisoba 🍜', 'yakisoba', '🍜', 'Yakisoba de carne, frango e legumes', 1, 1),
(9, 'Rolinhos Primavera 🥟', 'rolinhos-primavera', '🥟', 'Rolinhos fritos', 2, 1),
(9, 'Porções Chinesas 🍚', 'porcoes-chinesas', '🍚', 'Arroz frito, chop suey', 3, 1),

-- Doces & Bolos (categoria 10)
(10, 'Bolos 🎂', 'bolos', '🎂', 'Bolos caseiros', 1, 1),
(10, 'Doces Finos 🍬', 'doces-finos', '🍬', 'Brigadeiros, beijinhos', 2, 1),
(10, 'Tortas 🥧', 'tortas', '🥧', 'Tortas doces', 3, 1),
(10, 'Pudins 🍮', 'pudins', '🍮', 'Pudim de leite', 4, 1),

-- Árabe (categoria 11)
(11, 'Esfihas 🥙', 'esfihas', '🥙', 'Esfihas abertas e fechadas', 1, 1),
(11, 'Quibes 🧆', 'quibes', '🧆', 'Quibes fritos', 2, 1),

-- Frutos do Mar (categoria 12)
(12, 'Camarões 🦐', 'camaroes', '🦐', 'Camarões preparados', 1, 1),
(12, 'Peixes 🐟', 'peixes', '🐟', 'Filés de peixe', 2, 1),

-- Vegetariana (categoria 13)
(13, 'Saladas 🥗', 'saladas', '🥗', 'Saladas frescas', 1, 1),
(13, 'Hambúrguer Vegetal 🌱', 'hamburguer-vegetal', '🌱', 'Hambúrgueres veganos', 2, 1),

-- Bebidas (categoria 14)
(14, 'Refrigerantes 🥤', 'refrigerantes', '🥤', 'Coca, Guaraná, Fanta', 1, 1),
(14, 'Sucos Naturais 🧃', 'sucos', '🧃', 'Sucos naturais', 2, 1),
(14, 'Cervejas 🍺', 'cervejas', '🍺', 'Cervejas long neck', 3, 1),

-- Padaria (categoria 15)
(15, 'Pães 🥖', 'paes', '🥖', 'Pães artesanais', 1, 1),
(15, 'Salgados Assados 🥟', 'salgados-assados', '🥟', 'Salgados de forno', 2, 1),
(15, 'Doces de Padaria 🥐', 'doces-padaria', '🥐', 'Sonho, brigadeiro, tortinhas', 3, 1);


-- =====================================================
-- 35 LOJAS (1 a 35) - baseado nos produtos gerados
-- =====================================================

INSERT INTO loja (
    id, nome, descricao, slug, categoria, logo, capa,
    tempo_entrega_min, tempo_entrega_max, taxa_entrega, pedido_minimo,
    cep, logradouro, numero, complemento, bairro, cidade, uf,
    latitude, longitude,
    telefone, whatsapp, email, instagram,
    status, verificado, destaque, cor_tema, nota_media, total_avaliacoes, criado_em
) VALUES
-- LOJA 1: Pizzaria do João 🍕
(1, 'Pizzaria do João 🍕', 'A melhor pizza da cidade, com massa artesanal e ingredientes selecionados. Destaque para a pizza meio a meio!', 'pizzaria-do-joao', 'Pizzarias 🍕', 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=150', 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800', 30, 60, 8.50, 25.00, '30140-070', 'Av. Afonso Pena', '1000', 'Sala 2', 'Centro', 'Belo Horizonte', 'MG', -19.924722, -43.935278, '(31) 3222-1111', '(31) 98888-1111', 'contato@pizzariadojoao.com.br', '@pizzariadojoaobh', 'ativo', 1, 1, '#FF6B6B', 4.5, 128, NOW()),

-- LOJA 2: Dominus Pizza 🍕
(2, 'Dominus Pizza 🍕', 'Pizzas premium com ingredientes importados. Ambiente familiar.', 'dominus-pizza', 'Pizzarias 🍕', 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=150', 'https://images.unsplash.com/photo-1566843972143-a8a3b8182e4d?w=800', 25, 55, 7.00, 30.00, '30190-131', 'Rua Alagoas', '1000', 'Loja 2', 'Funcionários', 'Belo Horizonte', 'MG', -19.934444, -43.927500, '(31) 3222-2222', '(31) 98888-2222', 'contato@dominuspizza.com.br', '@dominuspizzabh', 'ativo', 1, 1, '#E63946', 4.8, 256, NOW()),

-- LOJA 3: Pizza Prime 🍕
(3, 'Pizza Prime 🍕', 'Especializada em pizzas doces e bordas recheadas. Ambiente moderno.', 'pizza-prime', 'Pizzarias 🍕', 'https://images.unsplash.com/photo-1579758629938-03607ccdbaba?w=150', 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=800', 20, 50, 6.00, 20.00, '30330-000', 'Rua Pernambuco', '500', 'Loja 3', 'Funcionários', 'Belo Horizonte', 'MG', -19.933333, -43.926389, '(31) 3222-3333', '(31) 98888-3333', 'contato@pizzaprime.com.br', '@pizzaprimebh', 'ativo', 1, 0, '#FF8C42', 4.2, 89, NOW()),

-- LOJA 4: La Pizza Mia 🍕
(4, 'La Pizza Mia 🍕', 'Pizzas no estilo napolitano, forno à lenha.', 'la-pizza-mia', 'Pizzarias 🍕', 'https://images.unsplash.com/photo-1590947132387-155cc02f3212?w=150', 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=800', 35, 65, 9.00, 35.00, '30180-090', 'Rua Rio de Janeiro', '1200', 'Sala 101', 'Centro', 'Belo Horizonte', 'MG', -19.917778, -43.931111, '(31) 3222-4444', '(31) 98888-4444', 'contato@lapizzamia.com.br', '@lapizzamiabh', 'ativo', 1, 1, '#A71E2E', 4.9, 312, NOW()),

-- LOJA 5: Hamburgueria do Zé 🍔
(5, 'Hamburgueria do Zé 🍔', 'Hambúrgueres artesanais com pão australiano e molho especial da casa. Tudo com muito queijo!', 'hamburgueria-do-ze', 'Hamburguerias 🍔', 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=150', 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=800', 20, 45, 6.00, 20.00, '30330-000', 'Rua Pernambuco', '500', 'Loja 5', 'Funcionários', 'Belo Horizonte', 'MG', -19.933333, -43.926389, '(31) 3222-5555', '(31) 98888-5555', 'contato@hamburgueriadoze.com.br', '@hamburgueriadozebh', 'ativo', 1, 1, '#FFA500', 4.6, 189, NOW()),

-- LOJA 6: The Burger House 🍔
(6, 'The Burger House 🍔', 'Hambúrgueres smash e artesanais. Destaque para o bacon crocante.', 'the-burger-house', 'Hamburguerias 🍔', 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=150', 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=800', 15, 40, 5.00, 18.00, '30140-071', 'Av. Afonso Pena', '1500', 'Loja 2', 'Centro', 'Belo Horizonte', 'MG', -19.923056, -43.936667, '(31) 3222-6666', '(31) 98888-6666', 'contato@theburgerhouse.com.br', '@theburgerhousebh', 'ativo', 1, 1, '#D97706', 4.7, 245, NOW()),

-- LOJA 7: Burger Lab 🍔
(7, 'Burger Lab 🍔', 'Hambúrgueres com receitas exclusivas, criadas em laboratório de sabores.', 'burger-lab', 'Hamburguerias 🍔', 'https://images.unsplash.com/photo-1586196717159-818ac123d8e4?w=150', 'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?w=800', 20, 50, 7.00, 22.00, '30160-011', 'Rua São Paulo', '1000', 'Térreo', 'Centro', 'Belo Horizonte', 'MG', -19.920278, -43.939167, '(31) 3222-7777', '(31) 98888-7777', 'contato@burgerlab.com.br', '@burgerlabbh', 'ativo', 1, 0, '#059669', 4.4, 67, NOW()),

-- LOJA 8: Green Burger 🌱
(8, 'Green Burger 🌱', 'Hambúrgueres vegetarianos e veganos. Opções saudáveis e saborosas.', 'green-burger', 'Hamburguerias 🍔', 'https://images.unsplash.com/photo-1610440042657-612c34d95a59?w=150', 'https://images.unsplash.com/photo-1525059696034-4967a8e1dca2?w=800', 15, 35, 5.00, 15.00, '30190-130', 'Rua Alagoas', '800', 'Loja B', 'Funcionários', 'Belo Horizonte', 'MG', -19.934722, -43.928333, '(31) 3222-8888', '(31) 98888-8888', 'contato@greenburger.com.br', '@greenburgerbh', 'ativo', 1, 0, '#16A34A', 4.3, 34, NOW()),

-- LOJA 9: Sushi Hakai BH 🍣
(9, 'Sushi Hakai BH 🍣', 'Sushi e culinária japonesa com peixes frescos diariamente. Combinados especiais para todos os gostos.', 'sushi-hakai-bh', 'Japonesa 🍣', 'https://images.unsplash.com/photo-1617196035154-1e7e6e28b0db?w=150', 'https://images.unsplash.com/photo-1617196034180-238e4746e8f0?w=800', 35, 55, 10.00, 40.00, '30180-090', 'Rua Rio de Janeiro', '1200', 'Sala 101', 'Centro', 'Belo Horizonte', 'MG', -19.917778, -43.931111, '(31) 3222-9999', '(31) 98888-9999', 'contato@sushihakai.com.br', '@sushihakaibh', 'ativo', 1, 1, '#FF4444', 4.8, 312, NOW()),

-- LOJA 10: Kenko Sushi 🍣
(10, 'Kenko Sushi 🍣', 'Sushi delivery com preço justo. Combinados executivos.', 'kenko-sushi', 'Japonesa 🍣', 'https://images.unsplash.com/photo-1625142814011-f4718c0f5c14?w=150', 'https://images.unsplash.com/photo-1633436375087-8265a5eeb89c?w=800', 30, 50, 8.00, 35.00, '30310-010', 'Rua Alvarenga Peixoto', '300', 'Loja 2', 'Santo Agostinho', 'Belo Horizonte', 'MG', -19.937222, -43.943333, '(31) 3233-1111', '(31) 97777-1111', 'contato@kenkosushi.com.br', '@kenkosushibh', 'ativo', 1, 1, '#E11D48', 4.6, 189, NOW()),

-- LOJA 11: Temaki House 🍥
(11, 'Temaki House 🍥', 'Especializada em temakis gigantes e sushis criativos.', 'temaki-house', 'Japonesa 🍣', 'https://images.unsplash.com/photo-1617196034797-6df0bcc5b2aa?w=150', 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=800', 20, 45, 7.00, 30.00, '30210-340', 'Rua Pouso Alegre', '150', '', 'Floresta', 'Belo Horizonte', 'MG', -19.918333, -43.921944, '(31) 3233-2222', '(31) 97777-2222', 'contato@temakihouse.com.br', '@temakihousebh', 'ativo', 1, 0, '#B91C1C', 4.5, 78, NOW()),

-- LOJA 12: Sushi da Hora 🍣
(12, 'Sushi da Hora 🍣', 'Promoções diárias e combos com preço especial.', 'sushi-da-hora', 'Japonesa 🍣', 'https://images.unsplash.com/photo-1607245899181-1c78d8eb299d?w=150', 'https://images.unsplash.com/photo-1604904612710-622acf5f9a08?w=800', 25, 50, 8.00, 25.00, '30130-110', 'Rua Tupis', '500', 'Loja 12', 'Centro', 'Belo Horizonte', 'MG', -19.921667, -43.941111, '(31) 3233-3333', '(31) 97777-3333', 'contato@sushidahora.com.br', '@sushidahorabh', 'ativo', 1, 0, '#DC2626', 4.2, 45, NOW()),

-- LOJA 13: Restaurante Sabor da Terra 🇧🇷
(13, 'Restaurante Sabor da Terra 🇧🇷', 'Comida caseira, buffet por quilo e pratos executivos. Comida mineira de verdade!', 'sabor-da-terra', 'Brasileira 🇧🇷', 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=150', 'https://images.unsplash.com/photo-1592861956120-e524fc739696?w=800', 25, 50, 7.50, 22.00, '30112-010', 'Av. Olegário Maciel', '800', 'Sala 3', 'Lourdes', 'Belo Horizonte', 'MG', -19.928056, -43.945833, '(31) 3233-4444', '(31) 97777-4444', 'contato@sabordaterra.com.br', '@sabordaterrabh', 'ativo', 1, 1, '#228B22', 4.7, 256, NOW()),

-- LOJA 14: Mineiríssimo 🏔️
(14, 'Mineiríssimo 🏔️', 'Comida típica mineira com frango com quiabo, tutu e torresmo.', 'mineirissimo', 'Brasileira 🇧🇷', 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=150', 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=800', 30, 60, 8.00, 25.00, '30190-100', 'Rua Sergipe', '800', 'Loja 5', 'Funcionários', 'Belo Horizonte', 'MG', -19.935278, -43.930278, '(31) 3233-5555', '(31) 97777-5555', 'contato@mineirissimo.com.br', '@mineirissimobh', 'ativo', 1, 1, '#0B5E42', 4.9, 189, NOW()),

-- LOJA 15: Feijoada da Tia 🥘
(15, 'Feijoada da Tia 🥘', 'Especializada em feijoada completa às quartas e sábados.', 'feijoada-da-tia', 'Brasileira 🇧🇷', 'https://images.unsplash.com/photo-1626082927381-49cd97f1bfcc?w=150', 'https://images.unsplash.com/photo-1604908176997-125f25cc813f?w=800', 40, 70, 10.00, 35.00, '30220-200', 'Rua Domingos Vieira', '300', 'Loja 3', 'Santa Efigênia', 'Belo Horizonte', 'MG', -19.917222, -43.910278, '(31) 3233-6666', '(31) 97777-6666', 'contato@feijoadadatia.com.br', '@feijoadadatia', 'ativo', 1, 0, '#854D0E', 4.8, 134, NOW()),

-- LOJA 16: Café Central ☕
(16, 'Café Central ☕', 'Café especial, salgados e sobremesas em ambiente aconchegante. O melhor café da região!', 'cafe-central', 'Cafeteria ☕', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=150', 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800', 15, 35, 5.50, 12.00, '30160-011', 'Rua São Paulo', '1000', 'Térreo', 'Centro', 'Belo Horizonte', 'MG', -19.920278, -43.939167, '(31) 3233-7777', '(31) 97777-7777', 'contato@cafecentral.com.br', '@cafecentralbh', 'ativo', 1, 1, '#8B4513', 4.6, 312, NOW()),

-- LOJA 17: Coffee Lab ☕
(17, 'Coffee Lab ☕', 'Torrefação própria e métodos de extração especiais.', 'coffee-lab', 'Cafeteria ☕', 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=150', 'https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=800', 10, 30, 4.00, 10.00, '30330-000', 'Rua Pernambuco', '500', 'Loja 2', 'Funcionários', 'Belo Horizonte', 'MG', -19.933333, -43.926389, '(31) 3233-8888', '(31) 97777-8888', 'contato@coffeelab.com.br', '@coffeelabbh', 'ativo', 1, 0, '#92400E', 4.5, 89, NOW()),

-- LOJA 18: Taco El Mexican 🌮
(18, 'Taco El Mexican 🌮', 'Autêntica culinária mexicana com tacos, burritos e nachos. Muita pimenta e sabor!', 'taco-el-mexican', 'Mexicana 🌮', 'https://images.unsplash.com/photo-1599974579688-8dbdd335c77f?w=150', 'https://images.unsplash.com/photo-1551504734-5ee1c4a2499e?w=800', 25, 45, 7.00, 25.00, '30190-131', 'Rua Alagoas', '1000', 'Loja 3', 'Funcionários', 'Belo Horizonte', 'MG', -19.934444, -43.927500, '(31) 3233-9999', '(31) 97777-9999', 'contato@tacoelmexican.com.br', '@tacoelmexicanbh', 'ativo', 1, 1, '#FF4500', 4.4, 112, NOW()),

-- LOJA 19: El Burrito 🌯
(19, 'El Burrito 🌯', 'Burritos gigantes e nachos com muito queijo.', 'el-burrito', 'Mexicana 🌮', 'https://images.unsplash.com/photo-1613514785940-daed07799b6b?w=150', 'https://images.unsplash.com/photo-1600880291319-1e2f6b4f1048?w=800', 20, 40, 6.50, 20.00, '30130-110', 'Rua Tupis', '500', 'Loja 5', 'Centro', 'Belo Horizonte', 'MG', -19.921667, -43.941111, '(31) 3244-1111', '(31) 96666-1111', 'contato@elburrito.com.br', '@elburritobh', 'ativo', 1, 0, '#C2410C', 4.2, 56, NOW()),

-- LOJA 20: Cantina da Nonna 🍝
(20, 'Cantina da Nonna 🍝', 'Massas frescas, risotos e polentas, receitas tradicionais italianas como a nonna fazia.', 'cantina-da-nonna', 'Italiana 🍝', 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=150', 'https://images.unsplash.com/photo-1579684947550-22e945225d9a?w=800', 35, 60, 9.00, 40.00, '30130-003', 'Rua dos Timbiras', '2000', '', 'Lourdes', 'Belo Horizonte', 'MG', -19.929167, -43.943889, '(31) 3244-2222', '(31) 96666-2222', 'contato@cantinadanonna.com.br', '@cantinadanonnabh', 'ativo', 1, 1, '#B22222', 4.9, 189, NOW()),

-- LOJA 21: Pasta & Vino 🍷
(21, 'Pasta & Vino 🍷', 'Massas artesanais e vinhos selecionados.', 'pasta-vino', 'Italiana 🍝', 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=150', 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800', 30, 55, 8.50, 35.00, '30360-150', 'Rua Paraíba', '600', 'Loja 2', 'Savassi', 'Belo Horizonte', 'MG', -19.937778, -43.936111, '(31) 3244-3333', '(31) 96666-3333', 'contato@pasta-vino.com.br', '@pastavinobh', 'ativo', 1, 0, '#A52A2A', 4.7, 98, NOW()),

-- LOJA 22: Açai do Bob 🥤
(22, 'Açai do Bob 🥤', 'Açaí cremoso com as melhores combinações e acompanhamentos. Diversos tamanhos e opções.', 'acai-do-bob', 'Açaí & Sorvetes 🥤', 'https://images.unsplash.com/photo-1590301157287-9a19ce2fb86d?w=150', 'https://images.unsplash.com/photo-1602525962720-19b004a4c5e0?w=800', 15, 30, 5.00, 15.00, '30190-130', 'Rua Alagoas', '800', 'Loja B', 'Funcionários', 'Belo Horizonte', 'MG', -19.934722, -43.928333, '(31) 3244-4444', '(31) 96666-4444', 'contato@acaidobob.com.br', '@acaidobbobh', 'ativo', 1, 1, '#8A2BE2', 4.7, 178, NOW()),

-- LOJA 23: Sorveteria Gelato Mio 🍨
(23, 'Sorveteria Gelato Mio 🍨', 'Sorvetes artesanais italianos, com frutas frescas e ingredientes importados. Mais de 30 sabores!', 'gelato-mio', 'Açaí & Sorvetes 🥤', 'https://images.unsplash.com/photo-1505394033641-40a6ad0368b7?w=150', 'https://images.unsplash.com/photo-1501443762994-82bd5dace89a?w=800', 10, 20, 4.00, 8.00, '30220-200', 'Rua Domingos Vieira', '300', 'Loja 3', 'Santa Efigênia', 'Belo Horizonte', 'MG', -19.917222, -43.910278, '(31) 3244-5555', '(31) 96666-5555', 'contato@gelatomio.com.br', '@gelatomiobh', 'ativo', 1, 1, '#FF69B4', 4.9, 267, NOW()),

-- LOJA 24: China Express 🥡
(24, 'China Express 🥡', 'Comida chinesa tradicional com delivery rápido e embalagens térmicas. Yakisoba é o forte!', 'china-express', 'Chinesa 🥡', 'https://images.unsplash.com/photo-1525755662773-98976ef1c363?w=150', 'https://images.unsplash.com/photo-1526318896980-cf78c088247c?w=800', 30, 55, 8.00, 35.00, '30180-091', 'Rua Rio de Janeiro', '1500', 'Sala 205', 'Centro', 'Belo Horizonte', 'MG', -19.917222, -43.931944, '(31) 3244-6666', '(31) 96666-6666', 'contato@chinaexpress.com.br', '@chinaexpressbh', 'ativo', 1, 0, '#DC143C', 4.3, 98, NOW()),

-- LOJA 25: China King 👑
(25, 'China King 👑', 'Rolinhos primavera e yakisoba premium.', 'china-king', 'Chinesa 🥡', 'https://images.unsplash.com/photo-1529042410759-befb1204b468?w=150', 'https://images.unsplash.com/photo-1526318896980-cf78c088247c?w=800', 25, 50, 7.00, 30.00, '30130-003', 'Rua dos Timbiras', '2000', 'Loja 1', 'Lourdes', 'Belo Horizonte', 'MG', -19.929167, -43.943889, '(31) 3244-7777', '(31) 96666-7777', 'contato@chinaking.com.br', '@chinakingbh', 'ativo', 1, 0, '#B22222', 4.1, 45, NOW()),

-- LOJA 26: Esfiharia do Líbano 🥙
(26, 'Esfiharia do Líbano 🥙', 'Esfihas abertas e fechadas, com massa fina e crocante.', 'esfiharia-do-libano', 'Árabe 🥙', 'https://images.unsplash.com/photo-1561651823-34f113022e9e?w=150', 'https://images.unsplash.com/photo-1594834749740-74b3f6764be4?w=800', 25, 45, 6.50, 20.00, '30130-110', 'Rua Tupis', '500', 'Loja 12', 'Centro', 'Belo Horizonte', 'MG', -19.921667, -43.941111, '(31) 3244-8888', '(31) 96666-8888', 'contato@esfhariadolibano.com.br', '@esfhariadolibano', 'ativo', 1, 1, '#DAA520', 4.6, 134, NOW()),

-- LOJA 27: Frutos do Mar 🦐
(27, 'Frutos do Mar 🦐', 'Peixes, camarões e frutos do mar frescos, preparados com chef especializado.', 'frutos-do-mar', 'Frutos do Mar 🦐', 'https://images.unsplash.com/photo-1579631542720-3a87824fff86?w=150', 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800', 40, 65, 10.00, 45.00, '30360-150', 'Rua Paraíba', '600', 'Sala 101', 'Savassi', 'Belo Horizonte', 'MG', -19.937778, -43.936111, '(31) 3244-9999', '(31) 96666-9999', 'contato@frutosdomar.com.br', '@frutosdomarbh', 'ativo', 1, 1, '#4682B4', 4.7, 156, NOW()),

-- LOJA 28: Empório da Cerveja 🍺
(28, 'Empório da Cerveja 🍺', 'Cervejas especiais nacionais e importadas. Delivery de drinks e petiscos.', 'emporio-da-cerveja', 'Bebidas 🧃', 'https://images.unsplash.com/photo-1518178935732-2b2f0d89e65d?w=150', 'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=800', 15, 40, 5.00, 25.00, '30160-011', 'Rua São Paulo', '1000', 'Loja 5', 'Centro', 'Belo Horizonte', 'MG', -19.920278, -43.939167, '(31) 3255-1111', '(31) 95555-1111', 'contato@emporiodacerveja.com.br', '@emporiodacervejabh', 'ativo', 1, 0, '#F59E0B', 4.4, 67, NOW()),

-- LOJA 29: Padaria Pão Quente 🥖
(29, 'Padaria Pão Quente 🥖', 'Pães artesanais, doces e salgados fresquinhos toda manhã.', 'pao-quente', 'Padaria 🥖', 'https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=150', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800', 10, 25, 4.50, 10.00, '30140-071', 'Av. Afonso Pena', '1500', 'Loja 1', 'Centro', 'Belo Horizonte', 'MG', -19.923056, -43.936667, '(31) 3255-2222', '(31) 95555-2222', 'contato@paoquente.com.br', '@paoquentebh', 'ativo', 1, 1, '#DEB887', 4.8, 234, NOW()),

-- LOJA 30: (adicional - espaço para mais uma)
(30, 'Empório Mineiro 🧀', 'Queijos, doces e cachaças de Minas Gerais.', 'emporio-mineiro', 'Brasileira 🇧🇷', 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=150', 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=800', 15, 30, 6.00, 20.00, '30140-070', 'Av. Afonso Pena', '1800', 'Loja 3', 'Centro', 'Belo Horizonte', 'MG', -19.924722, -43.935278, '(31) 3255-3333', '(31) 95555-3333', 'contato@emporiomineiro.com.br', '@emporiomineirobh', 'ativo', 1, 0, '#8B4513', 4.5, 89, NOW()),

-- LOJA 31: Burguer Vegano BH 🌱
(31, 'Burguer Vegano BH 🌱', 'Hambúrgueres 100% veganos com ingredientes orgânicos.', 'burguer-vegano-bh', 'Vegetariana 🌱', 'https://images.unsplash.com/photo-1610440042657-612c34d95a59?w=150', 'https://images.unsplash.com/photo-1525059696034-4967a8e1dca2?w=800', 20, 35, 5.00, 18.00, '30190-130', 'Rua Alagoas', '700', 'Loja 1', 'Funcionários', 'Belo Horizonte', 'MG', -19.934722, -43.928333, '(31) 3255-4444', '(31) 95555-4444', 'contato@burguervegano.com.br', '@burguerveganobh', 'ativo', 1, 1, '#2E7D32', 4.6, 67, NOW()),

-- LOJA 32: Salada & Cia 🥗
(32, 'Salada & Cia 🥗', 'Saladas frescas e bowls saudáveis para o dia a dia.', 'salada-cia', 'Vegetariana 🌱', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=150', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800', 10, 20, 4.00, 15.00, '30160-011', 'Rua São Paulo', '800', 'Loja 2', 'Centro', 'Belo Horizonte', 'MG', -19.920278, -43.939167, '(31) 3255-5555', '(31) 95555-5555', 'contato@saladacia.com.br', '@saladaciabh', 'ativo', 1, 0, '#689F38', 4.4, 45, NOW()),

-- LOJA 33: Sucos da Hora 🧃
(33, 'Sucos da Hora 🧃', 'Sucos naturais, vitaminas e bowls de frutas.', 'sucos-da-hora', 'Bebidas 🧃', 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=150', 'https://images.unsplash.com/photo-1613478225219-f65c1ee69c47?w=800', 5, 15, 3.00, 8.00, '30140-070', 'Av. Afonso Pena', '1200', 'Quiosque', 'Centro', 'Belo Horizonte', 'MG', -19.924722, -43.935278, '(31) 3255-6666', '(31) 95555-6666', 'contato@sucosdahora.com.br', '@sucosdahorabh', 'ativo', 1, 0, '#F9A825', 4.3, 34, NOW()),

-- LOJA 34: Cervejaria Artesanal BH 🍺
(34, 'Cervejaria Artesanal BH 🍺', 'Cervejas artesanais produzidas em BH. Chopp e petiscos.', 'cervejaria-artesanal-bh', 'Bebidas 🧃', 'https://images.unsplash.com/photo-1518178935732-2b2f0d89e65d?w=150', 'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=800', 20, 40, 7.00, 30.00, '30360-150', 'Rua Paraíba', '700', 'Loja 1', 'Savassi', 'Belo Horizonte', 'MG', -19.937778, -43.936111, '(31) 3255-7777', '(31) 95555-7777', 'contato@cervejariabh.com.br', '@cervejariartesanalbh', 'ativo', 1, 1, '#BF360C', 4.7, 112, NOW()),

-- LOJA 35: Doce Encanto 🎂
(35, 'Doce Encanto 🎂', 'Bolos caseiros, doces finos e sobremesas especiais.', 'doce-encanto', 'Doces & Bolos 🎂', 'https://images.unsplash.com/photo-1488477304112-4944851de03d?w=150', 'https://images.unsplash.com/photo-1488477304112-4944851de03d?w=800', 15, 25, 5.00, 12.00, '30190-100', 'Rua Sergipe', '600', 'Loja 3', 'Funcionários', 'Belo Horizonte', 'MG', -19.935278, -43.930278, '(31) 3255-8888', '(31) 95555-8888', 'contato@doceencanto.com.br', '@doceencantobh', 'ativo', 1, 1, '#C2185B', 4.8, 156, NOW());