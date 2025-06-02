import React, { useState } from 'react';
import { Table, Button, Form, Modal, Container, Row, Col } from 'react-bootstrap';

function Siswa() {
  const [siswaList, setSiswaList] = useState([
    { id: 1, nama: 'Budi Santoso', kelas: 'X RPL 1', alamat: 'Jl. Merdeka No. 123' },
    { id: 2, nama: 'Ani Wijaya', kelas: 'X RPL 1', alamat: 'Jl. Pahlawan No. 45' },
    { id: 3, nama: 'Dedi Kurniawan', kelas: 'X RPL 2', alamat: 'Jl. Sudirman No. 78' },
  ]);

  const [showModal, setShowModal] = useState(false);
  const [currentSiswa, setCurrentSiswa] = useState({ id: null, nama: '', kelas: '', alamat: '' });
  const [isEditing, setIsEditing] = useState(false);

  const handleClose = () => {
    setShowModal(false);
    setCurrentSiswa({ id: null, nama: '', kelas: '', alamat: '' });
    setIsEditing(false);
  };

  const handleShow = () => setShowModal(true);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setCurrentSiswa({ ...currentSiswa, [name]: value });
  };

  const handleSubmit = () => {
    if (isEditing) {
      // Update siswa
      setSiswaList(siswaList.map(siswa => 
        siswa.id === currentSiswa.id ? currentSiswa : siswa
      ));
    } else {
      // Add new siswa
      const newId = siswaList.length > 0 ? Math.max(...siswaList.map(s => s.id)) + 1 : 1;
      setSiswaList([...siswaList, { ...currentSiswa, id: newId }]);
    }
    handleClose();
  };

  const handleEdit = (siswa) => {
    setCurrentSiswa(siswa);
    setIsEditing(true);
    handleShow();
  };

  const handleDelete = (id) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) {
      setSiswaList(siswaList.filter(siswa => siswa.id !== id));
    }
  };

  return (
    <Container>
      <h1 style={{ fontSize: '2.5rem', color: '#0000ff', marginBottom: '20px' }}>Data Siswa</h1>
      <Button variant="primary" onClick={handleShow} className="mb-3">
        Tambah Siswa
      </Button>

      <Table striped bordered hover>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Alamat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {siswaList.map((siswa, index) => (
            <tr key={siswa.id}>
              <td>{index + 1}</td>
              <td>{siswa.nama}</td>
              <td>{siswa.kelas}</td>
              <td>{siswa.alamat}</td>
              <td>
                <Button variant="info" size="sm" className="me-2" onClick={() => handleEdit(siswa)}>
                  Edit
                </Button>
                <Button variant="danger" size="sm" onClick={() => handleDelete(siswa.id)}>
                  Hapus
                </Button>
              </td>
            </tr>
          ))}
        </tbody>
      </Table>

      {/* Modal untuk tambah/edit siswa */}
      <Modal show={showModal} onHide={handleClose}>
        <Modal.Header closeButton>
          <Modal.Title>{isEditing ? 'Edit Siswa' : 'Tambah Siswa'}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Form>
            <Form.Group className="mb-3">
              <Form.Label>Nama</Form.Label>
              <Form.Control
                type="text"
                name="nama"
                value={currentSiswa.nama}
                onChange={handleInputChange}
                placeholder="Masukkan nama siswa"
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Kelas</Form.Label>
              <Form.Control
                type="text"
                name="kelas"
                value={currentSiswa.kelas}
                onChange={handleInputChange}
                placeholder="Masukkan kelas"
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Alamat</Form.Label>
              <Form.Control
                as="textarea"
                name="alamat"
                value={currentSiswa.alamat}
                onChange={handleInputChange}
                placeholder="Masukkan alamat"
                rows={3}
              />
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

export default Siswa;