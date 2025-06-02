
import React, { useState } from 'react';
import { Container, Row, Col, Card, Button, Form, Modal, Tab, Tabs, InputGroup } from 'react-bootstrap';

function Menu() {
  // Data awal menu makanan
  const [makanan, setMakanan] = useState([
    { id: 1, nama: 'Nasi Goreng', harga: 15000, kategori: 'makanan' },
    { id: 2, nama: 'Mie Ayam', harga: 12000, kategori: 'makanan' },
    { id: 3, nama: 'Soto Ayam', harga: 10000, kategori: 'makanan' },
  ]);

  // Data awal menu buah
  const [buah, setBuah] = useState([
    { id: 1, nama: 'Apel', harga: 5000, kategori: 'buah' },
    { id: 2, nama: 'Jeruk', harga: 3000, kategori: 'buah' },
    { id: 3, nama: 'Mangga', harga: 7000, kategori: 'buah' },
  ]);

  // State untuk pencarian
  const [searchTerm, setSearchTerm] = useState('');
  
  // State untuk modal
  const [showModal, setShowModal] = useState(false);
  const [currentItem, setCurrentItem] = useState({ id: null, nama: '', harga: '', kategori: 'makanan' });
  const [isEditing, setIsEditing] = useState(false);

  // Filter buah berdasarkan pencarian
  const filteredBuah = buah.filter(item => 
    item.nama.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleClose = () => {
    setShowModal(false);
    setCurrentItem({ id: null, nama: '', harga: '', kategori: 'makanan' });
    setIsEditing(false);
  };

  const handleShow = (kategori) => {
    setCurrentItem({ ...currentItem, kategori });
    setShowModal(true);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setCurrentItem({ ...currentItem, [name]: name === 'harga' ? parseInt(value) || '' : value });
  };

  const handleSubmit = () => {
    if (isEditing) {
      // Update item
      if (currentItem.kategori === 'makanan') {
        setMakanan(makanan.map(item => 
          item.id === currentItem.id ? currentItem : item
        ));
      } else {
        setBuah(buah.map(item => 
          item.id === currentItem.id ? currentItem : item
        ));
      }
    } else {
      // Add new item
      if (currentItem.kategori === 'makanan') {
        const newId = makanan.length > 0 ? Math.max(...makanan.map(item => item.id)) + 1 : 1;
        setMakanan([...makanan, { ...currentItem, id: newId }]);
      } else {
        const newId = buah.length > 0 ? Math.max(...buah.map(item => item.id)) + 1 : 1;
        setBuah([...buah, { ...currentItem, id: newId }]);
      }
    }
    handleClose();
  };

  const handleEdit = (item) => {
    setCurrentItem(item);
    setIsEditing(true);
    setShowModal(true);
  };

  const handleDelete = (id, kategori) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus menu ini?')) {
      if (kategori === 'makanan') {
        setMakanan(makanan.filter(item => item.id !== id));
      } else {
        setBuah(buah.filter(item => item.id !== id));
      }
    }
  };

  return (
    <Container>
      <h1 style={{ fontSize: '2.5rem', color: '#0000ff', marginBottom: '20px' }}>Daftar Menu</h1>
      
      <Tabs defaultActiveKey="makanan" className="mb-3">
        <Tab eventKey="makanan" title="Menu Makanan">
          <Button variant="primary" onClick={() => handleShow('makanan')} className="mb-3">
            Tambah Menu Makanan
          </Button>
          
          <Row xs={1} md={3} className="g-4">
            {makanan.map((item) => (
              <Col key={item.id}>
                <Card>
                  <Card.Body>
                    <Card.Title>{item.nama}</Card.Title>
                    <Card.Text>
                      Harga: Rp {item.harga.toLocaleString('id-ID')}
                    </Card.Text>
                    <div className="d-flex gap-2">
                      <Button variant="info" size="sm" onClick={() => handleEdit(item)}>
                        Edit
                      </Button>
                      <Button variant="danger" size="sm" onClick={() => handleDelete(item.id, 'makanan')}>
                        Hapus
                      </Button>
                    </div>
                  </Card.Body>
                </Card>
              </Col>
            ))}
          </Row>
        </Tab>
        
        <Tab eventKey="buah" title="Menu Buah">
          <div className="d-flex justify-content-between mb-3">
            <Button variant="primary" onClick={() => handleShow('buah')}>
              Tambah Menu Buah
            </Button>
            
            <InputGroup style={{ width: '300px' }}>
              <Form.Control
                placeholder="Cari buah..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
              {searchTerm && (
                <Button variant="outline-secondary" onClick={() => setSearchTerm('')}>
                  X
                </Button>
              )}
            </InputGroup>
          </div>
          
          <Row xs={1} md={3} className="g-4">
            {filteredBuah.map((item) => (
              <Col key={item.id}>
                <Card>
                  <Card.Body>
                    <Card.Title>{item.nama}</Card.Title>
                    <Card.Text>
                      Harga: Rp {item.harga.toLocaleString('id-ID')}
                    </Card.Text>
                    <div className="d-flex gap-2">
                      <Button variant="info" size="sm" onClick={() => handleEdit(item)}>
                        Edit
                      </Button>
                      <Button variant="danger" size="sm" onClick={() => handleDelete(item.id, 'buah')}>
                        Hapus
                      </Button>
                    </div>
                  </Card.Body>
                </Card>
              </Col>
            ))}
          </Row>
          
          {filteredBuah.length === 0 && (
            <div className="text-center py-4">
              <p>Tidak ada buah yang sesuai dengan pencarian.</p>
            </div>
          )}
        </Tab>
      </Tabs>

      {/* Modal untuk tambah/edit menu */}
      <Modal show={showModal} onHide={handleClose}>
        <Modal.Header closeButton>
          <Modal.Title>
            {isEditing ? 'Edit Menu' : `Tambah Menu ${currentItem.kategori === 'makanan' ? 'Makanan' : 'Buah'}`}
          </Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Form>
            <Form.Group className="mb-3">
              <Form.Label>Nama</Form.Label>
              <Form.Control
                type="text"
                name="nama"
                value={currentItem.nama}
                onChange={handleInputChange}
                placeholder={`Masukkan nama ${currentItem.kategori}`}
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Harga (Rp)</Form.Label>
              <Form.Control
                type="number"
                name="harga"
                value={currentItem.harga}
                onChange={handleInputChange}
                placeholder="Masukkan harga"
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Kategori</Form.Label>
              <Form.Select
                name="kategori"
                value={currentItem.kategori}
                onChange={handleInputChange}
              >
                <option value="makanan">Makanan</option>
                <option value="buah">Buah</option>
              </Form.Select>
            </Form.Group>
          </Form>
        </Modal.Body>
        <Modal.Footer>
          <Button variant="secondary" onClick={handleClose}>
            Batal
          </Button>
          <Button variant="primary" onClick={handleSubmit}>
            Simpan
          </Button>
        </Modal.Footer>
      </Modal>
    </Container>
  );
}

export default Menu;