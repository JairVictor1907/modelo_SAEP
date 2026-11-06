CREATE DATABASE petshop
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE petshop;

CREATE TABLE tutores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    raca VARCHAR(80),
    idade INT,
    FOREIGN KEY (tutor_id) REFERENCES tutores(id)
);

CREATE TABLE IF NOT EXISTS atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    pet_id INT NOT NULL,
    data_atendimento DATE NOT NULL,
    servico VARCHAR(150) NOT NULL,
    status ENUM('Agendado', 'Realizado', 'Cancelado')
        NOT NULL DEFAULT 'Agendado',
    valor DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (tutor_id) REFERENCES tutores(id),
    FOREIGN KEY (pet_id) REFERENCES pets(id)
);
```