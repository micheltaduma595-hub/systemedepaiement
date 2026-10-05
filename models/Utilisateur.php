<?php
require_once __DIR__ . '/Model.php';

class Utilisateur extends Model {
    protected $table = 'utilisateurs';
    
    public function getAllWithRoles() {
        $sql = "SELECT u.*, 
                       GROUP_CONCAT(r.nom SEPARATOR ', ') as roles_noms,
                       GROUP_CONCAT(r.id SEPARATOR ',') as roles_ids
                FROM utilisateurs u
                LEFT JOIN utilisateur_roles ur ON u.id = ur.utilisateur_id
                LEFT JOIN roles r ON ur.role_id = r.id
                GROUP BY u.id
                ORDER BY u.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getUserWithRoles($id) {
        $sql = "SELECT u.*, 
                       GROUP_CONCAT(r.id) as role_ids
                FROM utilisateurs u
                LEFT JOIN utilisateur_roles ur ON u.id = ur.utilisateur_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.id = :id
                GROUP BY u.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function getRoles() {
        return $this->db->query("SELECT * FROM roles ORDER BY nom")->fetchAll();
    }
    
    public function createUser($data) {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        $this->db->beginTransaction();
        try {
            // Créer l'utilisateur
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
            
            // S'assurer que seules les colonnes existantes sont incluses
            $allowedFields = ['nom', 'email', 'mot_de_passe', 'telephone', 'actif'];
            $filteredData = array_intersect_key($data, array_flip($allowedFields));
            
            $columns = implode(', ', array_keys($filteredData));
            $placeholders = ':' . implode(', :', array_keys($filteredData));
            
            $stmt = $this->db->prepare("INSERT INTO utilisateurs ($columns) VALUES ($placeholders)");
            $stmt->execute($filteredData);
            $userId = $this->db->lastInsertId();
            
            // Assigner les rôles
            if (!empty($roles)) {
                $stmt = $this->db->prepare("INSERT INTO utilisateur_roles (utilisateur_id, role_id) VALUES (?, ?)");
                foreach ($roles as $roleId) {
                    $stmt->execute([$userId, $roleId]);
                }
            }
            
            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function updateUser($id, $data) {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        if (!empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        } else {
            unset($data['mot_de_passe']);
        }
        
        $this->db->beginTransaction();
        try {
            // Mettre à jour l'utilisateur
            $allowedFields = ['nom', 'email', 'mot_de_passe', 'telephone', 'actif'];
            $filteredData = array_intersect_key($data, array_flip($allowedFields));
            
            if (!empty($filteredData)) {
                $set = [];
                $params = [];
                foreach ($filteredData as $key => $value) {
                    $set[] = "$key = :$key";
                    $params[$key] = $value;
                }
                $params['id'] = $id;
                
                $stmt = $this->db->prepare("UPDATE utilisateurs SET " . implode(', ', $set) . " WHERE id = :id");
                $stmt->execute($params);
            }
            
            // Mettre à jour les rôles
            $stmt = $this->db->prepare("DELETE FROM utilisateur_roles WHERE utilisateur_id = ?");
            $stmt->execute([$id]);
            
            if (!empty($roles)) {
                $stmt = $this->db->prepare("INSERT INTO utilisateur_roles (utilisateur_id, role_id) VALUES (?, ?)");
                foreach ($roles as $roleId) {
                    $stmt->execute([$id, $roleId]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // CORRECTION : Méthode getLogsRecents avec LIMIT corrigée
    public function getLogsRecents($limit = 50) {
        // Convertir en entier pour éviter les erreurs SQL
        $limit = (int)$limit;
        
        $sql = "SELECT l.*, u.nom as utilisateur_nom
                FROM logs l
                LEFT JOIN utilisateurs u ON l.utilisateur_id = u.id
                ORDER BY l.created_at DESC
                LIMIT $limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}